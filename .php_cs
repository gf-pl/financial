<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src');

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        // Additional
        'strict_param' => true,
        'declare_strict_types' => true,
        'mb_str_functions' => true,
        'heredoc_to_nowdoc' => true,
        'linebreak_after_opening_tag' => true,
        'native_function_invocation' => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'phpdoc_add_missing_param_annotation' => [
            'only_untyped' => true
        ],
        'phpdoc_order' => true,
        'random_api_migration' => true,
        'simplified_null_return' => false,
        'ternary_to_null_coalescing' => true,
        'void_return' => true,
        // Changed
        'blank_line_before_statement' => [
            'statements' => ['return', 'throw', 'try']
        ],
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => [
            'spacing' => 'one'
        ],
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last'
        ],
        'visibility_required' => [
            'elements' => ['property', 'method']
        ],
        // Excluded
        'trailing_comma_in_multiline_array' => false,
        'blank_line_after_opening_tag' => false,
        'phpdoc_align' => false,
        // Discuss
        // function_declaration
        // ordered_class_elements
        // ordered_imports
        // phpdoc_no_empty_return
        // phpdoc_separation
        // strict_comparison
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
