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

use PhpParser\Lexer\Emulative;
use PhpParser\Parser\Php7;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\TraitUse;
use LogicException;
use PhpParser\Builder\Method;
use PhpParser\Node\NullableType;
use PhpParser\Builder\Property;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\Class_;
use Exception;
use PhpParser\Node\Stmt;
use ReflectionClass;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use ReflectionException;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitor\FirstFindingVisitor;
use PhpParser\NodeVisitor\FindingVisitor;
use DateTimeInterface;
use DateTimeImmutable;
use DateInterval;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Attribute;
use PhpParser\Node\Arg;
use ReflectionParameter;
use Doctrine\Common\Collections\ArrayCollection;
use PhpParser\Builder;
use PhpParser\BuilderHelpers;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Doctrine\BaseCollectionRelation;
use Symfony\Bundle\MakerBundle\Doctrine\BaseRelation;
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
    private readonly Php7 $parser;
    private readonly Emulative $lexer;
    private readonly PrettyPrinter $printer;
    private ?ConsoleStyle $io = null;

    private string $sourceCode;
    private $oldStmts;
    private array $oldTokens;
    private array $newStmts;

    private array $pendingComments = [];

    public function __construct(string $sourceCode, private readonly bool $overwrite = false, private readonly bool $useAnnotations = true, private readonly bool $useAttributesForDoctrineMapping = false)
    {
        $this->lexer = new Emulative([
            'usedAttributes' => [
                'comments',
                'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ],
        ]);
        $this->parser = new Php7($this->lexer);
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

    public function addCreatedToContructor(): void
    {
        $addCreatedAt = true;
        if ($this->getConstructorNode() instanceof ClassMethod) {
            // We print the constructor to a string, then
            // look for "$this->propertyName = "

            $constructorString = $this->printer->prettyPrint([$this->getConstructorNode()]);
            if (str_contains($constructorString, sprintf('$this->%s = ', 'createdAt'))) {
                $addCreatedAt = false;
            }
        }

        if ($addCreatedAt) {
            $this->addStatementToConstructor(
                new Expression(new Assign(
                    new PropertyFetch(new Variable('this'), 'createdAt'),
                    new New_(new Name('\DateTime'))
                ))
            );
        }
    }

    public function addEntityField(string $propertyName, array $columnOptions, array $comments = []): void
    {
        $typeHint = $this->getEntityTypeHint($columnOptions['type']);
        $attributes = [];

        if ($this->useAttributesForDoctrineMapping) {
            $attributes[] = $this->buildAttributeNode('ORM\Column', $columnOptions);
        } else {
            $comments[] = $this->buildAnnotationLine('@ORM\Column', $columnOptions);
        }

        $defaultValue = null;
        if ('array' === $typeHint) {
            $defaultValue = new Array_([], ['kind' => Array_::KIND_SHORT]);
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

        $this->getClassNode()->implements[] = new Name(Str::getShortClassName($interfaceName));
        $this->updateSourceCodeFromNewStmts();
    }

    /**
     * @param string $trait the fully-qualified trait name
     */
    public function addTrait(string $trait): void
    {
        $importedClassName = $this->addUseStatementIfNecessary($trait);

        /** @var TraitUse[] $traitNodes */
        $traitNodes = $this->findAllNodes(static fn($node): bool => $node instanceof TraitUse);

        foreach ($traitNodes as $node) {
            if ($node->traits[0]->toString() === $importedClassName) {
                return;
            }
        }

        $traitNodes[] = new TraitUse([new Name($importedClassName)]);

        $classNode = $this->getClassNode();

        if (!empty($classNode->stmts) && 1 === \count($traitNodes)) {
            $traitNodes[] = $this->createBlankLineNode(self::CONTEXT_CLASS);
        }

        // avoid all the use traits in class for unshift all the new UseTrait
        // in the right order.
        foreach ($classNode->stmts as $key => $node) {
            if ($node instanceof TraitUse) {
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
        if ($this->getConstructorNode() instanceof ClassMethod) {
            throw new LogicException('Constructor already exists.');
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
    public function addMethodBuilder(Method $methodBuilder, array $params = [], ?string $methodBody = null): void
    {
        $this->addMethodParams($methodBuilder, $params);

        if ($methodBody) {
            $this->addMethodBody($methodBuilder, $methodBody);
        }

        $this->addMethod($methodBuilder->getNode());
    }

    public function addMethodBody(Method $methodBuilder, string $methodBody): void
    {
        $nodes = $this->parser->parse($methodBody);
        $methodBuilder->addStmts($nodes);
    }

    public function createMethodBuilder(string $methodName, $returnType, bool $isReturnTypeNullable, array $commentLines = []): Method
    {
        $methodNodeBuilder = (new Method($methodName))
            ->makePublic()
        ;

        if (null !== $returnType) {
            if (class_exists($returnType) || interface_exists($returnType)) {
                $returnType = $this->addUseStatementIfNecessary($returnType);
            }
            $methodNodeBuilder->setReturnType($isReturnTypeNullable ? new NullableType($returnType) : $returnType);
        }

        if ($commentLines !== []) {
            $methodNodeBuilder->setDocComment($this->createDocBlock($commentLines));
        }

        return $methodNodeBuilder;
    }

    public function createMethodLevelCommentNode(string $comment): Stmt
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

        $newPropertyBuilder = (new Property($name))->makePrivate();

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
        if ([] === $docLines) {
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

            if ($extraComments !== '' && $extraComments !== '0') {
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
            if ($stmt instanceof Use_) {
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
            } elseif ($stmt instanceof Class_) {
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
            throw new Exception('Could not find a class!');
        }

        $newUseNode = (new Builder\Use_($class, Use_::TYPE_NORMAL))->getNode();
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
        $formattedOptions = array_map(function ($option, $value): string {
            if (\is_array($value)) {
                if (!isset($value[0])) {
                    return sprintf('%s={%s}', $option, implode(', ', array_map(fn($val, $key): string => sprintf('"%s" = %s', $key, $this->quoteAnnotationValue($val)), $value, array_keys($value))));
                }

                return sprintf('%s={%s}', $option, implode(', ', array_map(fn($val): int|string => $this->quoteAnnotationValue($val), $value)));
            }

            return sprintf('%s=%s', $option, $this->quoteAnnotationValue($value));
        }, array_keys($options), array_values($options));

        return sprintf('%s(%s)', $annotationClass, implode(', ', $formattedOptions));
    }

    private function quoteAnnotationValue($value): int|string
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
            throw new Exception('Invalid value: loop before quoting.');
        }

        return sprintf('"%s"', $value);
    }

    private function addSingularRelation(BaseRelation $relation): void
    {
        $typeHint = $this->addUseStatementIfNecessary($relation->getTargetClassName());
        if ($relation->getTargetClassName() === $this->getThisFullClassName()) {
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
        if ($this->getConstructorNode() instanceof ClassMethod) {
            // We print the constructor to a string, then
            // look for "$this->propertyName = "

            $constructorString = $this->printer->prettyPrint([$this->getConstructorNode()]);
            if (str_contains($constructorString, sprintf('$this->%s = ', $relation->getPropertyName()))) {
                $addArrayCollection = false;
            }
        }

        if ($addArrayCollection) {
            $this->addStatementToConstructor(
                new Expression(new Assign(
                    new PropertyFetch(new Variable('this'), $relation->getPropertyName()),
                    new New_(new Name($arrayCollectionTypeHint))
                ))
            );
        }

        Str::pluralCamelCaseToSingular($relation->getPropertyName());
    }

    private function addStatementToConstructor(Stmt $stmt): void
    {
        if (!$this->getConstructorNode() instanceof ClassMethod) {
            $constructorNode = (new Method('__construct'))->makePublic()->getNode();

            // add call to parent::__construct() if there is a need to
            try {
                $ref = new ReflectionClass($this->getThisFullClassName());

                if ($ref->getParentClass() && $ref->getParentClass()->getConstructor()) {
                    $constructorNode->stmts[] = new Expression(
                        new StaticCall(new Name('parent'), new Identifier('__construct'))
                    );
                }
            } catch (ReflectionException) {
            }

            $this->addNodeAfterProperties($constructorNode);
        }

        $constructorNode = $this->getConstructorNode();
        $constructorNode->stmts[] = $stmt;
        $this->updateSourceCodeFromNewStmts();
    }

    /**
     * @throws Exception
     */
    private function getConstructorNode(): ?ClassMethod
    {
        foreach ($this->getClassNode()->stmts as $classNode) {
            if ($classNode instanceof ClassMethod && '__construct' == $classNode->name) {
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
        $newCode = preg_replace('/use __EXTRA__LINE;/', '', (string) $newCode);
        $newCode = preg_replace('/(\ |\t)*\$__EXTRA__LINE;/', '', (string) $newCode);

        // process comment lines
        foreach ($this->pendingComments as $i => $comment) {
            // sanity check
            $placeholder = sprintf('$__COMMENT__VAR_%d;', $i);
            if (!str_contains((string) $newCode, $placeholder)) {
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
        $traverser->addVisitor(new CloningVisitor());
        $traverser->addVisitor(new NameResolver(null, [
            'replaceNodes' => false,
        ]));
        $this->newStmts = $traverser->traverse($this->oldStmts);
    }

    private function getClassNode(): Class_
    {
        $node = $this->findFirstNode(static fn($node): bool => $node instanceof Class_);

        if (!$node instanceof Node) {
            throw new Exception('Could not find class node');
        }

        /* @phpstan-ignore-next-line */
        return $node;
    }

    private function getNamespaceNode(): Namespace_
    {
        $node = $this->findFirstNode(static fn($node): bool => $node instanceof Namespace_);

        if (!$node instanceof Node) {
            throw new Exception('Could not find namespace node');
        }

        /* @phpstan-ignore-next-line */
        return $node;
    }

    private function findFirstNode(callable $filterCallback): ?Node
    {
        $traverser = new NodeTraverser();
        $visitor = new FirstFindingVisitor($filterCallback);
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->newStmts);

        return $visitor->getFoundNode();
    }

    private function findLastNode(callable $filterCallback, array $ast): ?Node
    {
        $traverser = new NodeTraverser();
        $visitor = new FindingVisitor($filterCallback);
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
        $visitor = new FindingVisitor($filterCallback);
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->newStmts);

        return $visitor->getFoundNodes();
    }

    private function createBlankLineNode(string $context)
    {
        return match ($context) {
            self::CONTEXT_OUTSIDE_CLASS => (new Builder\Use_('__EXTRA__LINE', Use_::TYPE_NORMAL))
                ->getNode(),
            self::CONTEXT_CLASS => (new Property('__EXTRA__LINE'))
                ->makePrivate()
                ->getNode(),
            self::CONTEXT_CLASS_METHOD => new Variable('__EXTRA__LINE'),
            default => throw new Exception('Unknown context: '.$context),
        };
    }

    private function createSingleLineCommentNode(string $comment, string $context): Stmt
    {
        $this->pendingComments[] = $comment;
        switch ($context) {
            case self::CONTEXT_OUTSIDE_CLASS:
                // just not needed yet
                throw new Exception('not supported');
            case self::CONTEXT_CLASS:
                // just not needed yet
                throw new Exception('not supported');
            case self::CONTEXT_CLASS_METHOD:
                return BuilderHelpers::normalizeStmt(new Variable(sprintf('__COMMENT__VAR_%d', \count($this->pendingComments) - 1)));
            default:
                throw new Exception('Unknown context: '.$context);
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

        return $docBlock . "\n */";
    }

    private function addMethod(ClassMethod $methodNode): void
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
        return match ($doctrineType) {
            'string', 'text', 'guid', 'bigint', 'decimal' => 'string',
            'array', 'simple_array', 'json', 'json_array' => 'array',
            'boolean' => 'bool',
            'integer', 'smallint' => 'int',
            'float' => 'float',
            'datetime', 'datetimetz', 'date', 'time' => '\\'.DateTimeInterface::class,
            'datetime_immutable', 'datetimetz_immutable', 'date_immutable', 'time_immutable' => '\\'.DateTimeImmutable::class,
            'dateinterval' => '\\'.DateInterval::class,
            default => null,
        };
    }

    private function isInSameNamespace(string $class): bool
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
        $targetNode = $this->findLastNode(static fn($node): bool => $node instanceof Node\Stmt\Property, [$classNode]);

        // otherwise, try to add after the last constant
        if (!$targetNode instanceof Node) {
            $targetNode = $this->findLastNode(static fn($node): bool => $node instanceof ClassConst, [$classNode]);
        }

        // otherwise, try to add after the last trait
        if (!$targetNode instanceof Node) {
            $targetNode = $this->findLastNode(static fn($node): bool => $node instanceof TraitUse, [$classNode]);
        }

        // add the new property after this node
        if ($targetNode instanceof Node) {
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
            if ($node instanceof ClassMethod && strtolower($node->name->toString()) === strtolower($methodName)) {
                return $i;
            }
        }

        return false;
    }

    private function propertyExists(string $propertyName): bool
    {
        foreach ($this->getClassNode()->stmts as $node) {
            if ($node instanceof Node\Stmt\Property && $node->props[0]->name->toString() === $propertyName) {
                return true;
            }
        }

        return false;
    }

    private function writeNote(string $note): void
    {
        if ($this->io instanceof ConsoleStyle) {
            $this->io->text($note);
        }
    }

    private function addMethodParams(Method $methodBuilder, array $params): void
    {
        foreach ($params as $param) {
            $methodBuilder->addParam($param);
        }
    }

    /**
     * builds a PHPParser Expr Node based on the value given in $value
     * throws an Exception when the given $value is not resolvable by this method.
     *
     *
     * @throws Exception
     */
    private function buildNodeExprByValue(mixed $value): Expr
    {
        switch (\gettype($value)) {
            case 'string':
                $nodeValue = new String_($value);
                break;
            case 'integer':
                $nodeValue = new LNumber($value);
                break;
            case 'double':
                $nodeValue = new DNumber($value);
                break;
            case 'boolean':
                $nodeValue = new ConstFetch(new Name($value ? 'true' : 'false'));
                break;
            case 'array':
                $context = $this;
                $arrayItems = array_map(static fn($key, $value): ArrayItem => new ArrayItem(
                    $context->buildNodeExprByValue($value),
                    \is_int($key) ? null : $context->buildNodeExprByValue($key)
                ), array_keys($value), array_values($value));
                $nodeValue = new Array_($arrayItems, ['kind' => Array_::KIND_SHORT]);
                break;
            default:
                $nodeValue = null;
        }

        if (null === $nodeValue) {
            if ($value instanceof ClassNameValue) {
                $nodeValue = new ConstFetch(
                    new Name(
                        sprintf('%s::class', $value->isSelf() ? 'self' : $value->getShortName())
                    )
                );
            } else {
                throw new Exception(sprintf('Cannot build a node expr for value of type "%s"', \gettype($value)));
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
    private function buildAttributeNode(string $attributeClass, array $options): Attribute
    {
        $options = $this->sortOptionsByClassConstructorParameters($options, $attributeClass);

        $context = $this;
        $nodeArguments = array_map(static fn($option, $value): Arg => new Arg($context->buildNodeExprByValue($value), false, false, [], new Identifier($option)), array_keys($options), array_values($options));

        return new Attribute(
            new Name($attributeClass),
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
        if (str_starts_with($classString, 'ORM\\')) {
            $classString = sprintf('Doctrine\\ORM\\Mapping\\%s', substr($classString, 4));
        }

        $constructorParameterNames = array_map(static fn(ReflectionParameter $reflectionParameter): string => $reflectionParameter->getName(), (new ReflectionClass($classString))->getConstructor()->getParameters());

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
