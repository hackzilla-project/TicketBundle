<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Maker\Util;

use Doctrine\Common\Collections\ArrayCollection;
use PhpParser\Builder;
use PhpParser\BuilderHelpers;
use PhpParser\Comment\Doc;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Parser;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Doctrine\BaseCollectionRelation;
use Symfony\Bundle\MakerBundle\Doctrine\BaseRelation;
use Symfony\Bundle\MakerBundle\Doctrine\BaseSingleRelation;
use Symfony\Bundle\MakerBundle\Doctrine\RelationManyToMany;
use Symfony\Bundle\MakerBundle\Doctrine\RelationManyToOne;
use Symfony\Bundle\MakerBundle\Doctrine\RelationOneToMany;
use Symfony\Bundle\MakerBundle\Doctrine\RelationOneToOne;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameValue;
use Symfony\Bundle\MakerBundle\Util\PrettyPrinter;

/**
 * @internal
 *
 * A lot of this class is a duplication of the Symfony Maker component.
 * My hope is the Maker component will eventually open up as it becomes more mature
 *
 * Other reasons to duplicate this code is to stop the creation of getters and setters, as well as stop duplicate addConstructor
 */
final class ClassSourceManipulator
{
    private const CONTEXT_OUTSIDE_CLASS = 'outside_class';
    private const CONTEXT_CLASS = 'class';
    private const CONTEXT_CLASS_METHOD = 'class_method';

    private $overwrite;
    private $useAnnotations;
    private $useAttributesForDoctrineMapping;
    private $parser;
    private $lexer;
    private $printer;
    /** @var ConsoleStyle|null */
    private $io;

    private $sourceCode;
    private $oldStmts;
    private $oldTokens;
    private $newStmts;

    private $pendingComments = [];

    public function __construct(string $sourceCode, bool $overwrite = false, bool $useAnnotations = true, bool $useAttributesForDoctrineMapping = false)
    {
        $this->overwrite = $overwrite;
        $this->useAnnotations = $useAnnotations;
        $this->useAttributesForDoctrineMapping = $useAttributesForDoctrineMapping;
        $this->lexer = new Lexer\Emulative([
            'usedAttributes' => [
                'comments',
                'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ],
        ]);
        $this->parser = new Parser\Php7($this->lexer);
        $this->printer = new PrettyPrinter();

        $this->setSourceCode($sourceCode);
    }

    public function setIo(ConsoleStyle $io): void
    {
        $this->io = $io;
    }

    public function getSourceCode(): string
    {
        return $this->sourceCode;
    }

    public function addCreatedToContructor()
    {
        $addCreatedAt = true;
        if ($this->getConstructorNode()) {
            // We print the constructor to a string, then
            // look for "$this->propertyName = "

            $constructorString = $this->printer->prettyPrint([$this->getConstructorNode()]);
            if (false !== strpos($constructorString, sprintf('$this->%s = ', 'createdAt'))) {
                $addCreatedAt = false;
            }
        }

        if ($addCreatedAt) {
            $this->addStatementToConstructor(
                new Node\Stmt\Expression(new Node\Expr\Assign(
                    new Node\Expr\PropertyFetch(new Node\Expr\Variable('this'), 'createdAt'),
                    new Node\Expr\New_(new Node\Name('\DateTime'))
                ))
            );
        }
    }

    public function addEntityField(string $propertyName, array $columnOptions, array $comments = []): void
    {
        $typeHint = $this->getEntityTypeHint($columnOptions['type']);
        $nullable = $columnOptions['nullable'] ?? false;
        $isId = (bool) ($columnOptions['id'] ?? false);
        $attributes = [];

        if ($this->useAttributesForDoctrineMapping) {
            $attributes[] = $this->buildAttributeNode('ORM\Column', $columnOptions);
        } else {
            $comments[] = $this->buildAnnotationLine('@ORM\Column', $columnOptions);
        }

        $defaultValue = null;
        if ('array' === $typeHint) {
            $defaultValue = new Node\Expr\Array_([], ['kind' => Node\Expr\Array_::KIND_SHORT]);
        }

        $this->addProperty($propertyName, $comments, $defaultValue, $attributes);
    }

    public function addManyToOneRelation(RelationManyToOne $manyToOne): void
    {
        $this->addSingularRelation($manyToOne);
    }

    public function addOneToOneRelation(RelationOneToOne $oneToOne): void
    {
        $this->addSingularRelation($oneToOne);
    }

    public function addOneToManyRelation(RelationOneToMany $oneToMany): void
    {
        $this->addCollectionRelation($oneToMany);
    }

    public function addManyToManyRelation(RelationManyToMany $manyToMany): void
    {
        $this->addCollectionRelation($manyToMany);
    }

    public function addInterface(string $interfaceName): void
    {
        $this->addUseStatementIfNecessary($interfaceName);

        foreach ($this->getClassNode()->implements as $node) {
            if (implode('\\', $node->getAttribute('resolvedName')->parts) === $interfaceName) {
                return;
            }
        }

        $this->getClassNode()->implements[] = new Node\Name(Str::getShortClassName($interfaceName));
        $this->updateSourceCodeFromNewStmts();
    }

    /**
     * @param string $trait the fully-qualified trait name
     */
    public function addTrait(string $trait): void
    {
        $importedClassName = $this->addUseStatementIfNecessary($trait);

        /** @var Node\Stmt\TraitUse[] $traitNodes */
        $traitNodes = $this->findAllNodes(static function ($node) {
            return $node instanceof Node\Stmt\TraitUse;
        });

        foreach ($traitNodes as $node) {
            if ($node->traits[0]->toString() === $importedClassName) {
                return;
            }
        }

        $traitNodes[] = new Node\Stmt\TraitUse([new Node\Name($importedClassName)]);

        $classNode = $this->getClassNode();

        if (!empty($classNode->stmts) && 1 === \count($traitNodes)) {
            $traitNodes[] = $this->createBlankLineNode(self::CONTEXT_CLASS);
        }

        // avoid all the use traits in class for unshift all the new UseTrait
        // in the right order.
        foreach ($classNode->stmts as $key => $node) {
            if ($node instanceof Node\Stmt\TraitUse) {
                unset($classNode->stmts[$key]);
            }
        }

        array_unshift($classNode->stmts, ...$traitNodes);

        $this->updateSourceCodeFromNewStmts();
    }

    /**
     * @param Node[] $params
     */
    public function addConstructor(array $params, string $methodBody): void
    {
        if (null !== $this->getConstructorNode()) {
            throw new \LogicException('Constructor already exists.');
        }

        $methodBuilder = $this->createMethodBuilder('__construct', null, false);

        $this->addMethodParams($methodBuilder, $params);

        $this->addMethodBody($methodBuilder, $methodBody);

        $this->addNodeAfterProperties($methodBuilder->getNode());
        $this->updateSourceCodeFromNewStmts();
    }

    /**
     * @param Node[] $params
     */
    public function addMethodBuilder(Builder\Method $methodBuilder, array $params = [], ?string $methodBody = null): void
    {
        $this->addMethodParams($methodBuilder, $params);

        if ($methodBody) {
            $this->addMethodBody($methodBuilder, $methodBody);
        }

        $this->addMethod($methodBuilder->getNode());
    }

    public function addMethodBody(Builder\Method $methodBuilder, string $methodBody): void
    {
        $nodes = $this->parser->parse($methodBody);
        $methodBuilder->addStmts($nodes);
    }

    public function createMethodBuilder(string $methodName, $returnType, bool $isReturnTypeNullable, array $commentLines = []): Builder\Method
    {
        $methodNodeBuilder = (new Builder\Method($methodName))
            ->makePublic()
        ;

        if (null !== $returnType) {
            if (class_exists($returnType) || interface_exists($returnType)) {
                $returnType = $this->addUseStatementIfNecessary($returnType);
            }
            $methodNodeBuilder->setReturnType($isReturnTypeNullable ? new Node\NullableType($returnType) : $returnType);
        }

        if ($commentLines) {
            $methodNodeBuilder->setDocComment($this->createDocBlock($commentLines));
        }

        return $methodNodeBuilder;
    }

    public function createMethodLevelCommentNode(string $comment)
    {
        return $this->createSingleLineCommentNode($comment, self::CONTEXT_CLASS_METHOD);
    }

    public function createMethodLevelBlankLine()
    {
        return $this->createBlankLineNode(self::CONTEXT_CLASS_METHOD);
    }

    public function addProperty(string $name, array $annotationLines = [], $defaultValue = null, array $attributes = []): void
    {
        if ($this->propertyExists($name)) {
            // we never overwrite properties
            return;
        }

        $newPropertyBuilder = (new Builder\Property($name))->makePrivate();

        if ($annotationLines && $this->useAnnotations) {
            $newPropertyBuilder->setDocComment($this->createDocBlock($annotationLines));
        }

        foreach ($attributes as $attribute) {
            $newPropertyBuilder->addAttribute($attribute);
        }

        if (null !== $defaultValue) {
            $newPropertyBuilder->setDefault($defaultValue);
        }
        $newPropertyNode = $newPropertyBuilder->getNode();

        $this->addNodeAfterProperties($newPropertyNode);
    }

    public function addAnnotationToClass(string $annotationClass, array $options): void
    {
        $annotationClassAlias = $this->addUseStatementIfNecessary($annotationClass);
        $docComment = $this->getClassNode()->getDocComment();

        $docLines = $docComment ? explode("\n", $docComment->getText()) : [];
        if (0 === \count($docLines)) {
            $docLines = ['/**', ' */'];
        } elseif (1 === \count($docLines)) {
            // /** inline doc syntax */
            // imperfect way to try to find where to split the lines
            $endOfOpening = strpos($docLines[0], '* ');
            $endingPosition = strrpos($docLines[0], ' *', $endOfOpening);
            $extraComments = trim(substr($docLines[0], $endOfOpening + 2, $endingPosition - $endOfOpening - 2));
            $newDocLines = [
                substr($docLines[0], 0, $endOfOpening + 1),
            ];

            if ($extraComments) {
                $newDocLines[] = ' * '.$extraComments;
            }

            $newDocLines[] = substr($docLines[0], $endingPosition);
            $docLines = $newDocLines;
        }

        array_splice(
            $docLines,
            \count($docLines) - 1,
            0,
            ' * '.$this->buildAnnotationLine('@'.$annotationClassAlias, $options)
        );

        $docComment = new Doc(implode("\n", $docLines));
        $this->getClassNode()->setDocComment($docComment);
        $this->updateSourceCodeFromNewStmts();
    }

    /**
     * @return string The alias to use when referencing this class
     */
    public function addUseStatementIfNecessary(string $class): string
    {
        $shortClassName = Str::getShortClassName($class);
        if ($this->isInSameNamespace($class)) {
            return $shortClassName;
        }

        $namespaceNode = $this->getNamespaceNode();

        $targetIndex = null;
        $addLineBreak = false;
        $lastUseStmtIndex = null;
        foreach ($namespaceNode->stmts as $index => $stmt) {
            if ($stmt instanceof Node\Stmt\Use_) {
                // I believe this is an array to account for use statements with {}
                foreach ($stmt->uses as $use) {
                    $alias = $use->alias ? $use->alias->name : $use->name->getLast();

                    // the use statement already exists? Don't add it again
                    if ($class === (string) $use->name) {
                        return $alias;
                    }

                    if ($alias === $shortClassName) {
                        // we have a conflicting alias!
                        // to be safe, use the fully-qualified class name
                        // everywhere and do not add another use statement
                        return '\\'.$class;
                    }
                }

                // if $class is alphabetically before this use statement, place it before
                // only set $targetIndex the first time you find it
                if (null === $targetIndex && Str::areClassesAlphabetical($class, (string) $stmt->uses[0]->name)) {
                    $targetIndex = $index;
                }

                $lastUseStmtIndex = $index;
            } elseif ($stmt instanceof Node\Stmt\Class_) {
                if (null !== $targetIndex) {
                    // we already found where to place the use statement

                    break;
                }

                // we hit the class! If there were any use statements,
                // then put this at the bottom of the use statement list
                if (null !== $lastUseStmtIndex) {
                    $targetIndex = $lastUseStmtIndex + 1;
                } else {
                    $targetIndex = $index;
                    $addLineBreak = true;
                }

                break;
            }
        }

        if (null === $targetIndex) {
            throw new \Exception('Could not find a class!');
        }

        $newUseNode = (new Builder\Use_($class, Node\Stmt\Use_::TYPE_NORMAL))->getNode();
        array_splice(
            $namespaceNode->stmts,
            $targetIndex,
            0,
            $addLineBreak ? [$newUseNode, $this->createBlankLineNode(self::CONTEXT_OUTSIDE_CLASS)] : [$newUseNode]
        );

        $this->updateSourceCodeFromNewStmts();

        return $shortClassName;
    }

    /**
     * @param string $annotationClass The annotation: e.g. "@ORM\Column"
     * @param array  $options         Key-value pair of options for the annotation
     */
    private function buildAnnotationLine(string $annotationClass, array $options): string
    {
        $formattedOptions = array_map(function ($option, $value) {
            if (\is_array($value)) {
                if (!isset($value[0])) {
                    return sprintf('%s={%s}', $option, implode(', ', array_map(function ($val, $key) {
                        return sprintf('"%s" = %s', $key, $this->quoteAnnotationValue($val));
                    }, $value, array_keys($value))));
                }

                return sprintf('%s={%s}', $option, implode(', ', array_map(function ($val) {
                    return $this->quoteAnnotationValue($val);
                }, $value)));
            }

            return sprintf('%s=%s', $option, $this->quoteAnnotationValue($value));
        }, array_keys($options), array_values($options));

        return sprintf('%s(%s)', $annotationClass, implode(', ', $formattedOptions));
    }

    private function quoteAnnotationValue($value)
    {
        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (null === $value) {
            return 'null';
        }

        if (\is_int($value) || '0' === $value) {
            return $value;
        }

        if ($value instanceof ClassNameValue) {
            return sprintf('%s::class', $value->getShortName());
        }

        if (\is_array($value)) {
            throw new \Exception('Invalid value: loop before quoting.');
        }

        return sprintf('"%s"', $value);
    }

    private function addSingularRelation(BaseRelation $relation): void
    {
        $typeHint = $this->addUseStatementIfNecessary($relation->getTargetClassName());
        if ($relation->getTargetClassName() == $this->getThisFullClassName()) {
            $typeHint = 'self';
        }

        $annotationOptions = [
            'targetEntity' => new ClassNameValue($typeHint, $relation->getTargetClassName()),
        ];
        if ($relation->isOwning()) {
            // sometimes, we don't map the inverse relation
            if ($relation->getMapInverseRelation()) {
                $annotationOptions['inversedBy'] = $relation->getTargetPropertyName();
            }
        } else {
            $annotationOptions['mappedBy'] = $relation->getTargetPropertyName();
        }

        if ($relation instanceof RelationOneToOne) {
            $annotationOptions['cascade'] = ['persist', 'remove'];
        }

        $annotations = [];
        $attributes = [];

        if (!$this->useAttributesForDoctrineMapping) {
            $annotations = [
                $this->buildAnnotationLine(
                    $relation instanceof RelationManyToOne ? '@ORM\\ManyToOne' : '@ORM\\OneToOne',
                    $annotationOptions
                ),
            ];
        } else {
            $attributes = [
                $this->buildAttributeNode(
                    $relation instanceof RelationManyToOne ? 'ORM\\ManyToOne' : 'ORM\\OneToOne',
                    $annotationOptions
                ),
            ];
        }

        $this->addProperty($relation->getPropertyName(), $annotations, null, $attributes);
    }

    private function addCollectionRelation(BaseCollectionRelation $relation): void
    {
        $typeHint = $relation->isSelfReferencing() ? 'self' : $this->addUseStatementIfNecessary($relation->getTargetClassName());

        $arrayCollectionTypeHint = $this->addUseStatementIfNecessary(ArrayCollection::class);

        $annotationOptions = [
            'targetEntity' => new ClassNameValue($typeHint, $relation->getTargetClassName()),
        ];
        if ($relation->isOwning()) {
            // sometimes, we don't map the inverse relation
            if ($relation->getMapInverseRelation()) {
                $annotationOptions['inversedBy'] = $relation->getTargetPropertyName();
            }
        } else {
            $annotationOptions['mappedBy'] = $relation->getTargetPropertyName();
        }

        if ($relation->getOrphanRemoval()) {
            $annotationOptions['orphanRemoval'] = true;
        }

        $annotations = [];
        $attributes = [];

        if (!$this->useAttributesForDoctrineMapping) {
            $annotations = [
                $this->buildAnnotationLine(
                    $relation instanceof RelationManyToMany ? '@ORM\\ManyToMany' : '@ORM\\OneToMany',
                    $annotationOptions
                ),
            ];
        } else {
            $attributes = [
                $this->buildAttributeNode(
                    $relation instanceof RelationManyToMany ? 'ORM\\ManyToMany' : 'ORM\\OneToMany',
                    $annotationOptions
                ),
            ];
        }

        $this->addProperty($relation->getPropertyName(), $annotations, null, $attributes);

        // logic to avoid re-adding the same ArrayCollection line
        $addArrayCollection = true;
        if ($this->getConstructorNode()) {
            // We print the constructor to a string, then
            // look for "$this->propertyName = "

            $constructorString = $this->printer->prettyPrint([$this->getConstructorNode()]);
            if (false !== strpos($constructorString, sprintf('$this->%s = ', $relation->getPropertyName()))) {
                $addArrayCollection = false;
            }
        }

        if ($addArrayCollection) {
            $this->addStatementToConstructor(
                new Node\Stmt\Expression(new Node\Expr\Assign(
                    new Node\Expr\PropertyFetch(new Node\Expr\Variable('this'), $relation->getPropertyName()),
                    new Node\Expr\New_(new Node\Name($arrayCollectionTypeHint))
                ))
            );
        }

        $argName = Str::pluralCamelCaseToSingular($relation->getPropertyName());
    }

    private function addStatementToConstructor(Node\Stmt $stmt): void
    {
        if (!$this->getConstructorNode()) {
            $constructorNode = (new Builder\Method('__construct'))->makePublic()->getNode();

            // add call to parent::__construct() if there is a need to
            try {
                $ref = new \ReflectionClass($this->getThisFullClassName());

                if ($ref->getParentClass() && $ref->getParentClass()->getConstructor()) {
                    $constructorNode->stmts[] = new Node\Stmt\Expression(
                        new Node\Expr\StaticCall(new Node\Name('parent'), new Node\Identifier('__construct'))
                    );
                }
            } catch (\ReflectionException $e) {
            }

            $this->addNodeAfterProperties($constructorNode);
        }

        $constructorNode = $this->getConstructorNode();
        $constructorNode->stmts[] = $stmt;
        $this->updateSourceCodeFromNewStmts();
    }

    /**
     * @throws \Exception
     */
    private function getConstructorNode(): ?Node\Stmt\ClassMethod
    {
        foreach ($this->getClassNode()->stmts as $classNode) {
            if ($classNode instanceof Node\Stmt\ClassMethod && '__construct' == $classNode->name) {
                return $classNode;
            }
        }

        return null;
    }

    private function updateSourceCodeFromNewStmts(): void
    {
        $newCode = $this->printer->printFormatPreserving(
            $this->newStmts,
            $this->oldStmts,
            $this->oldTokens
        );

        // replace the 3 "fake" items that may be in the code (allowing for different indentation)
        $newCode = preg_replace('/(\ |\t)*private\ \$__EXTRA__LINE;/', '', $newCode);
        $newCode = preg_replace('/use __EXTRA__LINE;/', '', $newCode);
        $newCode = preg_replace('/(\ |\t)*\$__EXTRA__LINE;/', '', $newCode);

        // process comment lines
        foreach ($this->pendingComments as $i => $comment) {
            // sanity check
            $placeholder = sprintf('$__COMMENT__VAR_%d;', $i);
            if (false === strpos($newCode, $placeholder)) {
                // this can happen if a comment is createSingleLineCommentNode()
                // is called, but then that generated code is ultimately not added
                continue;
            }

            $newCode = str_replace($placeholder, '// '.$comment, $newCode);
        }
        $this->pendingComments = [];

        $this->setSourceCode($newCode);
    }

    private function setSourceCode(string $sourceCode): void
    {
        $this->sourceCode = $sourceCode;
        $this->oldStmts = $this->parser->parse($sourceCode);
        $this->oldTokens = $this->lexer->getTokens();

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NodeVisitor\CloningVisitor());
        $traverser->addVisitor(new NodeVisitor\NameResolver(null, [
            'replaceNodes' => false,
        ]));
        $this->newStmts = $traverser->traverse($this->oldStmts);
    }

    private function getClassNode(): Node\Stmt\Class_
    {
        $node = $this->findFirstNode(static function ($node) {
            return $node instanceof Node\Stmt\Class_;
        });

        if (!$node) {
            throw new \Exception('Could not find class node');
        }

        /* @phpstan-ignore-next-line */
        return $node;
    }

    private function getNamespaceNode(): Node\Stmt\Namespace_
    {
        $node = $this->findFirstNode(static function ($node) {
            return $node instanceof Node\Stmt\Namespace_;
        });

        if (!$node) {
            throw new \Exception('Could not find namespace node');
        }

        /* @phpstan-ignore-next-line */
        return $node;
    }

    private function findFirstNode(callable $filterCallback): ?Node
    {
        $traverser = new NodeTraverser();
        $visitor = new NodeVisitor\FirstFindingVisitor($filterCallback);
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->newStmts);

        return $visitor->getFoundNode();
    }

    private function findLastNode(callable $filterCallback, array $ast): ?Node
    {
        $traverser = new NodeTraverser();
        $visitor = new NodeVisitor\FindingVisitor($filterCallback);
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        $nodes = $visitor->getFoundNodes();
        $node = end($nodes);

        return false === $node ? null : $node;
    }

    /**
     * @return Node[]
     */
    private function findAllNodes(callable $filterCallback): array
    {
        $traverser = new NodeTraverser();
        $visitor = new NodeVisitor\FindingVisitor($filterCallback);
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->newStmts);

        return $visitor->getFoundNodes();
    }

    private function createBlankLineNode(string $context)
    {
        switch ($context) {
            case self::CONTEXT_OUTSIDE_CLASS:
                return (new Builder\Use_('__EXTRA__LINE', Node\Stmt\Use_::TYPE_NORMAL))
                    ->getNode()
                ;
            case self::CONTEXT_CLASS:
                return (new Builder\Property('__EXTRA__LINE'))
                    ->makePrivate()
                    ->getNode()
                ;
            case self::CONTEXT_CLASS_METHOD:
                return new Node\Expr\Variable('__EXTRA__LINE');
            default:
                throw new \Exception('Unknown context: '.$context);
        }
    }

    private function createSingleLineCommentNode(string $comment, string $context): Node\Stmt
    {
        $this->pendingComments[] = $comment;
        switch ($context) {
            case self::CONTEXT_OUTSIDE_CLASS:
                // just not needed yet
                throw new \Exception('not supported');
            case self::CONTEXT_CLASS:
                // just not needed yet
                throw new \Exception('not supported');
            case self::CONTEXT_CLASS_METHOD:
                return BuilderHelpers::normalizeStmt(new Node\Expr\Variable(sprintf('__COMMENT__VAR_%d', \count($this->pendingComments) - 1)));
            default:
                throw new \Exception('Unknown context: '.$context);
        }
    }

    private function createDocBlock(array $commentLines): string
    {
        $docBlock = "/**\n";
        foreach ($commentLines as $commentLine) {
            if ($commentLine) {
                $docBlock .= " * ${commentLine}\n";
            } else {
                // avoid the empty, extra space on blank lines
                $docBlock .= " *\n";
            }
        }
        $docBlock .= "\n */";

        return $docBlock;
    }

    private function addMethod(Node\Stmt\ClassMethod $methodNode): void
    {
        $classNode = $this->getClassNode();
        $methodName = $methodNode->name;
        $existingIndex = null;
        if ($this->methodExists($methodName)) {
            if (!$this->overwrite) {
                $this->writeNote(sprintf(
                    'Not generating <info>%s::%s()</info>: method already exists',
                    Str::getShortClassName($this->getThisFullClassName()),
                    $methodName
                ));

                return;
            }

            // record, so we can overwrite in the same place
            $existingIndex = $this->getMethodIndex($methodName);
        }

        $newStatements = [];

        // put new method always at the bottom
        if (!empty($classNode->stmts)) {
            $newStatements[] = $this->createBlankLineNode(self::CONTEXT_CLASS);
        }

        $newStatements[] = $methodNode;

        if (null === $existingIndex) {
            // add them to the end!

            $classNode->stmts = array_merge($classNode->stmts, $newStatements);
        } else {
            array_splice(
                $classNode->stmts,
                $existingIndex,
                1,
                $newStatements
            );
        }

        $this->updateSourceCodeFromNewStmts();
    }

    private function getEntityTypeHint($doctrineType): ?string
    {
        switch ($doctrineType) {
            case 'string':
            case 'text':
            case 'guid':
            case 'bigint':
            case 'decimal':
                return 'string';

            case 'array':
            case 'simple_array':
            case 'json':
            case 'json_array':
                return 'array';

            case 'boolean':
                return 'bool';

            case 'integer':
            case 'smallint':
                return 'int';

            case 'float':
                return 'float';

            case 'datetime':
            case 'datetimetz':
            case 'date':
            case 'time':
                return '\\'.\DateTimeInterface::class;

            case 'datetime_immutable':
            case 'datetimetz_immutable':
            case 'date_immutable':
            case 'time_immutable':
                return '\\'.\DateTimeImmutable::class;

            case 'dateinterval':
                return '\\'.\DateInterval::class;

            case 'object':
            case 'binary':
            case 'blob':
            default:
                return null;
        }
    }

    private function isInSameNamespace($class): bool
    {
        $namespace = substr($class, 0, strrpos($class, '\\'));

        return $this->getNamespaceNode()->name->toCodeString() === $namespace;
    }

    private function getThisFullClassName(): string
    {
        return (string) $this->getClassNode()->namespacedName;
    }

    /**
     * Adds this new node where a new property should go.
     *
     * Useful for adding properties, or adding a constructor.
     */
    private function addNodeAfterProperties(Node $newNode): void
    {
        $classNode = $this->getClassNode();

        // try to add after last property
        $targetNode = $this->findLastNode(static function ($node) {
            return $node instanceof Node\Stmt\Property;
        }, [$classNode]);

        // otherwise, try to add after the last constant
        if (!$targetNode) {
            $targetNode = $this->findLastNode(static function ($node) {
                return $node instanceof Node\Stmt\ClassConst;
            }, [$classNode]);
        }

        // otherwise, try to add after the last trait
        if (!$targetNode) {
            $targetNode = $this->findLastNode(static function ($node) {
                return $node instanceof Node\Stmt\TraitUse;
            }, [$classNode]);
        }

        // add the new property after this node
        if ($targetNode) {
            $index = array_search($targetNode, $classNode->stmts, true);

            array_splice(
                $classNode->stmts,
                $index + 1,
                0,
                [$this->createBlankLineNode(self::CONTEXT_CLASS), $newNode]
            );

            $this->updateSourceCodeFromNewStmts();

            return;
        }

        // put right at the beginning of the class
        // add an empty line, unless the class is totally empty
        if (!empty($classNode->stmts)) {
            array_unshift($classNode->stmts, $this->createBlankLineNode(self::CONTEXT_CLASS));
        }
        array_unshift($classNode->stmts, $newNode);
        $this->updateSourceCodeFromNewStmts();
    }

    private function methodExists(string $methodName): bool
    {
        return false !== $this->getMethodIndex($methodName);
    }

    private function getMethodIndex(string $methodName)
    {
        foreach ($this->getClassNode()->stmts as $i => $node) {
            if ($node instanceof Node\Stmt\ClassMethod && strtolower($node->name->toString()) === strtolower($methodName)) {
                return $i;
            }
        }

        return false;
    }

    private function propertyExists(string $propertyName): bool
    {
        foreach ($this->getClassNode()->stmts as $i => $node) {
            if ($node instanceof Node\Stmt\Property && $node->props[0]->name->toString() === $propertyName) {
                return true;
            }
        }

        return false;
    }

    private function writeNote(string $note): void
    {
        if (null !== $this->io) {
            $this->io->text($note);
        }
    }

    private function addMethodParams(Builder\Method $methodBuilder, array $params): void
    {
        foreach ($params as $param) {
            $methodBuilder->addParam($param);
        }
    }

    /**
     * builds a PHPParser Expr Node based on the value given in $value
     * throws an Exception when the given $value is not resolvable by this method.
     *
     * @param mixed $value
     *
     * @throws \Exception
     */
    private function buildNodeExprByValue($value): Node\Expr
    {
        switch (\gettype($value)) {
            case 'string':
                $nodeValue = new Node\Scalar\String_($value);
                break;
            case 'integer':
                $nodeValue = new Node\Scalar\LNumber($value);
                break;
            case 'double':
                $nodeValue = new Node\Scalar\DNumber($value);
                break;
            case 'boolean':
                $nodeValue = new Node\Expr\ConstFetch(new Node\Name($value ? 'true' : 'false'));
                break;
            case 'array':
                $context = $this;
                $arrayItems = array_map(static function ($key, $value) use ($context) {
                    return new Node\Expr\ArrayItem(
                        $context->buildNodeExprByValue($value),
                        !\is_int($key) ? $context->buildNodeExprByValue($key) : null
                    );
                }, array_keys($value), array_values($value));
                $nodeValue = new Node\Expr\Array_($arrayItems, ['kind' => Node\Expr\Array_::KIND_SHORT]);
                break;
            default:
                $nodeValue = null;
        }

        if (null === $nodeValue) {
            if ($value instanceof ClassNameValue) {
                $nodeValue = new Node\Expr\ConstFetch(
                    new Node\Name(
                        sprintf('%s::class', $value->isSelf() ? 'self' : $value->getShortName())
                    )
                );
            } else {
                throw new \Exception(sprintf('Cannot build a node expr for value of type "%s"', \gettype($value)));
            }
        }

        return $nodeValue;
    }

    /**
     * builds an PHPParser attribute node.
     *
     * @param string $attributeClass the attribute class which should be used for the attribute
     * @param array  $options        the named arguments for the attribute ($key = argument name, $value = argument value)
     */
    private function buildAttributeNode(string $attributeClass, array $options): Node\Attribute
    {
        $options = $this->sortOptionsByClassConstructorParameters($options, $attributeClass);

        $context = $this;
        $nodeArguments = array_map(static function ($option, $value) use ($context) {
            return new Node\Arg($context->buildNodeExprByValue($value), false, false, [], new Node\Identifier($option));
        }, array_keys($options), array_values($options));

        return new Node\Attribute(
            new Node\Name($attributeClass),
            $nodeArguments
        );
    }

    /**
     * sort the given options based on the constructor parameters for the given $classString
     * this prevents code inspections warnings for IDEs like intellij/phpstorm.
     *
     * option keys that are not found in the constructor will be added at the end of the sorted array
     */
    private function sortOptionsByClassConstructorParameters(array $options, string $classString): array
    {
        if ('ORM\\' === substr($classString, 0, 4)) {
            $classString = sprintf('Doctrine\\ORM\\Mapping\\%s', substr($classString, 4));
        }

        $constructorParameterNames = array_map(static function (\ReflectionParameter $reflectionParameter) {
            return $reflectionParameter->getName();
        }, (new \ReflectionClass($classString))->getConstructor()->getParameters());

        $sorted = [];
        foreach ($constructorParameterNames as $name) {
            if (\array_key_exists($name, $options)) {
                $sorted[$name] = $options[$name];
                unset($options[$name]);
            }
        }

        return array_merge($sorted, $options);
    }
}
