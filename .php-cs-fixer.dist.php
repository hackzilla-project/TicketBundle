<?php

$header = <<<'HEADER'
This file is part of HackzillaTicketBundle package.

(c) Daniel Platt <github@ofdan.co.uk>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude([
        'Maker',
        'Tests/Functional/cache',
    ])
;

$config = new PhpCsFixer\Config();
return $config
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP70Migration' => true,
        '@PHP70Migration:risky' => true,
        '@PHP71Migration' => true,
        '@PHP71Migration:risky' => true,
        '@PHP73Migration' => true,
        '@PHPUnit57Migration:risky' => true,
        '@PHPUnit60Migration:risky' => true,
        '@PHPUnit75Migration:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_after_opening_tag' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'compact_nullable_typehint' => true,
        'explicit_string_variable' => true,
        'global_namespace_import' => ['import_classes' => false],
        'header_comment' => [
            'header' => $header,
        ],
        'logical_operators' => true,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        'no_alternative_syntax' => true,
        'no_extra_blank_lines' => true,
        'no_null_property_initialization' => true,
        'no_php4_constructor' => true,
        'echo_tag_syntax' => ['format' => 'long'],
        'no_superfluous_elseif' => true,
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true],
        'no_unset_on_property' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'nullable_type_declaration_for_default_null_value' => ['use_nullable_type_declaration' => true],
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'php_unit_construct' => true,
        'php_unit_set_up_tear_down_visibility' => true,
        'php_unit_strict' => false,
        'phpdoc_order' => true,
        'single_line_throw' => false,
        'static_lambda' => true,
        'strict_param' => true,
        'void_return' => false,
    ])
    ->setFinder($finder)
;
