<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    ->withPaths([__DIR__])
    ->withSkip([__DIR__ . '/vendor'])
    // uncomment to reach your current PHP version
//    ->withTypeCoverageLevel(0)
    ->withPhpSets(php83: true)
    ->withPreparedSets(deadCode: true, codeQuality: true, typeDeclarations: true, doctrineCodeQuality: true, symfonyCodeQuality: true, symfonyConfigs: true, twig: true, phpunit: true)
    ->withImportNames(importNames: true, removeUnusedImports: true)
    ->withSets([
        LevelSetList::UP_TO_PHP_74,
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        SetList::TYPE_DECLARATION,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
    ]);
