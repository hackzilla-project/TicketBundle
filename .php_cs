<?php

// Use PHP-CS-Fixer 2+ if it is available
if (\class_exists(PhpCsFixer\Config::class, false)) {
    return PhpCsFixer\Config::create()
        ->setUsingCache(true)
        ->setRiskyAllowed(true)
        ->setRules([
            '@Symfony' => true,
            'array_syntax' => ['syntax' => 'short'],
            'binary_operator_spaces' => [
                'align_double_arrow' => true,
                'align_equals' => true,
            ],
            'blank_line_after_opening_tag' => true,
            'ordered_imports' => true,
            'php_unit_construct' => true,
        ])
        ->setFinder(
            PhpCsFixer\Finder::create()->exclude(['Tests/Functional/cache'])->in(__DIR__)
        )
    ;
}

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->fixers([
        'align_double_arrow',
        'newline_after_open_tag',
        'ordered_use',
        'php_unit_construct',
        'short_array_syntax',
        '-unalign_double_arrow',
        '-unalign_equals',
    ])
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->exclude(['Tests/Functional/cache'])
            ->in(__DIR__)
    )
;
