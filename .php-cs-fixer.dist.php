<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_trailing_whitespace' => true, // Remove trailing whitespace
        'no_unused_imports' => true, // Remove unused imports
        'single_quote' => true,
        'ternary_operator_spaces' => true, // Ensure single space around ternary operators
        'blank_line_after_namespace' => true, // Ensure a blank line after namespace declaration
        'blank_line_after_opening_tag' => true, // Ensure a blank line after the opening PHP tag
        'blank_line_before_statement' => [
            'statements' => ['return']
        ], // Ensure a blank line before return statements
        'braces' => [
            'position_after_functions_and_oop_constructs' => 'next',
            'position_after_control_structures' => 'same',
            'position_after_anonymous_constructs' => 'same'
        ],
        'class_attributes_separation' => [
            'elements' => ['method' => 'one']
        ], // Ensure one blank line between class methods
        'concat_space' => ['spacing' => 'one'], // Ensure single space around concatenation operators
        'declare_equal_normalize' => ['space' => 'none'], // Normalize declare statements
        'function_declaration' => ['closure_function_spacing' => 'none'], // Control spacing in function declarations
        'include' => true, // Ensure include/require statements are formatted correctly
        'lowercase_cast' => true, // Ensure cast operators are in lowercase
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'], // Control spacing in method arguments
        'ternary_operator_spaces' => true, // Ensure single space around ternary operators
        'trailing_comma_in_multiline' => ['elements' => ['arrays']], // Ensure trailing comma in multiline arrays
        'trim_array_spaces' => true, // Remove spaces inside array brackets
        'unary_operator_spaces' => true, // Ensure single space around unary operators
        // Add more rules as needed
    ])
    ->setFinder($finder);
