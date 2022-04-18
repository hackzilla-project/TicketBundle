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

namespace Hackzilla\Bundle\TicketBundle\Maker;

use Hackzilla\Bundle\TicketBundle\Maker\Util\ClassSourceManipulator;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Doctrine\EntityClassGenerator;
use Symfony\Bundle\MakerBundle\Doctrine\EntityRegenerator;
use Symfony\Bundle\MakerBundle\Doctrine\EntityRelation;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassDetails;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * A lot of this class is a duplication of the Symfony Maker component.
 * My hope is the Maker component will eventually open up as it becomes more mature.
 */
abstract class AbstractMaker extends \Symfony\Bundle\MakerBundle\Maker\AbstractMaker
{
    private $fileManager;
    private $doctrineHelper;
    private $entityClassGenerator;
    private $userClass;
    private $ticketClass;
    private $messageClass;

    public function __construct(FileManager $fileManager, DoctrineHelper $doctrineHelper, EntityClassGenerator $entityClassGenerator, ParameterBagInterface $bag)
    {
        $this->fileManager = $fileManager;
        $this->doctrineHelper = $doctrineHelper;
        $this->entityClassGenerator = $entityClassGenerator;

        $this->userClass = $bag->get('hackzilla_ticket.model.user.class');
        $this->ticketClass = $bag->get('hackzilla_ticket.model.ticket.class');
        $this->messageClass = $bag->get('hackzilla_ticket.model.message.class');
    }

    public function getUserClass(): string
    {
        return $this->userClass;
    }

    public function getTicketClass(): string
    {
        return $this->ticketClass;
    }

    public function getMessageClass(): string
    {
        return $this->messageClass;
    }

    /**
     * Configure the command: set description, input arguments, options, etc.
     *
     * By default, all arguments will be asked interactively. If you want
     * to avoid that, use the $inputConfig->setArgumentAsNonInteractive() method.
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->addOption('api-resource', 'a', InputOption::VALUE_NONE, 'Mark this class as an API Platform resource (expose a CRUD API for it)')
            ->addOption('overwrite', null, InputOption::VALUE_NONE, 'Overwrite any existing getter/setter methods');
    }

    /**
     * Configure any library dependencies that your maker requires.
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {
    }

    /**
     * If necessary, you can use this method to interactively ask the user for input.
     */
    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
    }

    /**
     * Called after normal code generation: allows you to do anything.
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $entityClass = $this->entityClass();

        if (!class_exists($entityClass)) {
            throw new RuntimeCommandException(sprintf('The class %s doesn\'t not exist, please create this first.', $entityClass));
        }

        $overwrite = $input->getOption('overwrite');
        $entityClassDetails = new ClassNameDetails($entityClass, substr($entityClass, 0, strrpos($entityClass, '\\') + 1));

        if (!$this->doctrineHelper->isDoctrineSupportingAttributes() && $this->doctrineHelper->doesClassUsesAttributes($entityClassDetails->getFullName())) {
            throw new RuntimeCommandException('To use Doctrine entity attributes you\'ll need PHP 8, doctrine/orm 2.9, doctrine/doctrine-bundle 2.4 and symfony/framework-bundle 5.2.');
        }

        if (
            !$this->doesEntityUseAnnotationMapping($entityClassDetails->getFullName())
            && !$this->doesEntityUseAttributeMapping($entityClassDetails->getFullName())
        ) {
            throw new RuntimeCommandException(sprintf('Only annotation or attribute mapping is supported by this command, but the <info>%s</info> class uses a different format.', $entityClassDetails->getFullName()));
        }

        $entityPath = $this->getPathOfClass($entityClassDetails->getFullName());

        $currentFields = $this->getPropertyNames($entityClassDetails->getFullName());
        $manipulator = $this->createClassManipulator($entityPath, $io, $overwrite, $entityClassDetails->getFullName());

        foreach ($this->fields() as $newField) {
            $io->comment(is_a($newField, EntityRelation::class) ? $newField->getOwningProperty() : $newField['fieldName']);
            $fileManagerOperations = [];
            $fileManagerOperations[$entityPath] = $manipulator;

            if (\is_array($newField)) {
                $annotationOptions = $newField;
                unset($annotationOptions['fieldName']);
                $manipulator->addEntityField($newField['fieldName'], $annotationOptions);

                $currentFields[] = $newField['fieldName'];
            } elseif ($newField instanceof EntityRelation) {
                // both overridden below for OneToMany
                $newFieldName = $newField->getOwningProperty();
                if ($newField->isSelfReferencing()) {
                    $otherManipulatorFilename = $entityPath;
                    $otherManipulator = $manipulator;
                } else {
                    $otherManipulatorFilename = $this->getPathOfClass($newField->getInverseClass());
                    $otherManipulator = $this->createClassManipulator($otherManipulatorFilename, $io, $overwrite, $entityClassDetails->getFullName(), false);
                }
                switch ($newField->getType()) {
                    case EntityRelation::MANY_TO_ONE:
                        if ($newField->getOwningClass() === $entityClassDetails->getFullName()) {
                            // THIS class will receive the ManyToOne
                            $manipulator->addManyToOneRelation($newField->getOwningRelation());

                            if ($newField->getMapInverseRelation()) {
                                $otherManipulator->addOneToManyRelation($newField->getInverseRelation());
                            }
                        } else {
                            // the new field being added to THIS entity is the inverse
                            $newFieldName = $newField->getInverseProperty();
                            $otherManipulatorFilename = $this->getPathOfClass($newField->getOwningClass());
                            $otherManipulator = $this->createClassManipulator($otherManipulatorFilename, $io, $overwrite, $entityClassDetails->getFullName(), false);

                            // The *other* class will receive the ManyToOne
                            $otherManipulator->addManyToOneRelation($newField->getOwningRelation());
                            if (!$newField->getMapInverseRelation()) {
                                throw new \Exception('Somehow a OneToMany relationship is being created, but the inverse side will not be mapped?');
                            }
                            $manipulator->addOneToManyRelation($newField->getInverseRelation());
                        }

                        break;
                    case EntityRelation::MANY_TO_MANY:
                        $manipulator->addManyToManyRelation($newField->getOwningRelation());
                        if ($newField->getMapInverseRelation()) {
                            $otherManipulator->addManyToManyRelation($newField->getInverseRelation());
                        }

                        break;
                    case EntityRelation::ONE_TO_ONE:
                        $manipulator->addOneToOneRelation($newField->getOwningRelation());
                        if ($newField->getMapInverseRelation()) {
                            $otherManipulator->addOneToOneRelation($newField->getInverseRelation());
                        }

                        break;
                    default:
                        throw new \Exception('Invalid relation type');
                }

                // save the inverse side if it's being mapped
                if ($newField->getMapInverseRelation()) {
                    $fileManagerOperations[$otherManipulatorFilename] = $otherManipulator;
                }
                $currentFields[] = $newFieldName;
            } else {
                throw new \Exception('Invalid value');
            }

            foreach ($fileManagerOperations as $path => $manipulatorOrMessage) {
                if (\is_string($manipulatorOrMessage)) {
                    $io->comment($manipulatorOrMessage);
                } else {
                    $this->fileManager->dumpFile($path, $manipulatorOrMessage->getSourceCode());
                }
            }
        }

        $this->writeSuccessMessage($io);
        $io->text([
            'Next: When you\'re ready, create a migration with <info>php bin/console make:migration</info>',
            '',
        ]);
    }

    abstract protected function fields(): array;

    abstract protected function entityClass(): string;

    abstract protected function traits(): array;

    abstract protected function interfaces(): array;

    private function createClassManipulator(string $path, ConsoleStyle $io, bool $overwrite, string $className, bool $originalClass = true): ClassSourceManipulator
    {
        $useAttributes = $this->doctrineHelper->doesClassUsesAttributes($className) && $this->doctrineHelper->isDoctrineSupportingAttributes();
        $useAnnotations = $this->doctrineHelper->isClassAnnotated($className) || !$useAttributes;

        $manipulator = new ClassSourceManipulator($this->fileManager->getFileContents($path), $overwrite, $useAnnotations, true, $useAttributes);

        if ($originalClass) {
            foreach ($this->traits() as $trait) {
                $manipulator->addTrait($trait);
            }

            foreach ($this->interfaces() as $interface) {
                $manipulator->addInterface($interface);
            }
        }

        $manipulator->addCreatedToContructor();
        $manipulator->setIo($io);

        return $manipulator;
    }

    private function getPathOfClass(string $class): string
    {
        $classDetails = new ClassDetails($class);

        return $classDetails->getPath();
    }

    private function getPropertyNames(string $class): array
    {
        if (!class_exists($class)) {
            return [];
        }

        $reflClass = new \ReflectionClass($class);

        return array_map(static function (\ReflectionProperty $prop) {
            return $prop->getName();
        }, $reflClass->getProperties());
    }

    private function doesEntityUseAnnotationMapping(string $className): bool
    {
        if (!class_exists($className)) {
            $otherClassMetadatas = $this->doctrineHelper->getMetadata(Str::getNamespace($className).'\\', true);

            // if we have no metadata, we should assume this is the first class being mapped
            if (empty($otherClassMetadatas)) {
                return false;
            }

            $className = reset($otherClassMetadatas)->getName();
        }

        return $this->doctrineHelper->isClassAnnotated($className);
    }

    private function doesEntityUseAttributeMapping(string $className): bool
    {
        if (\PHP_VERSION < 80000) {
            return false;
        }

        if (!class_exists($className)) {
            $otherClassMetadatas = $this->doctrineHelper->getMetadata(Str::getNamespace($className).'\\', true);

            // if we have no metadata, we should assume this is the first class being mapped
            if (empty($otherClassMetadatas)) {
                return false;
            }

            $className = reset($otherClassMetadatas)->getName();
        }

        return $this->doctrineHelper->doesClassUsesAttributes($className);
    }
}
