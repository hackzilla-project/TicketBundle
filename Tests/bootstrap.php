<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

if (is_file('vendor/autoload.php')) {
    $loader = include 'vendor/autoload.php';
} elseif (!is_file($autoloadFile = __DIR__.'/../vendor/autoload.php')) {
    $loader = require $autoloadFile;
} else {
    throw new \LogicException('Run "composer install --dev" to create autoloader.');
}

// auto-load annotations
AnnotationRegistry::registerLoader([$loader, 'loadClass']);
