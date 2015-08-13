<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in([__DIR__])
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        '-psr0',
        'newline_after_open_tag',
        'ordered_use',
        'long_array_syntax',
        'php_unit_construct',
        'php_unit_strict'
    ])
    ->setUsingCache(true)
    ->finder($finder)
;
