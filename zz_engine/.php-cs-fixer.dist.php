<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('node_modules')
    ->exclude('var')
    ->exclude('docker')
    ->exclude('vendor')
    ->exclude('src/Service/System/HealthCheck/HealthChecker/Settings/Mock')
    ->notPath('src/Secondary/FileUpload/FileUploader.php')
;

$config = new PhpCsFixer\Config();
return $config
    ->setLineEnding("\n") // Linux LF line ending
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        '@Symfony' => true,
        '@PHP71Migration:risky' => true,
        'ordered_class_elements' => [
            'order' => [
                'use_trait', // traits

                'constant_public', // constants
                'constant_protected',
                'constant_private',

                'property_public_static', // static properties
                'property_protected_static',
                'property_private_static',

                'property_public', // properties
                'property_protected',
                'property_private',

                'construct', // magic methods
                'destruct',
                'magic',

                'method_public_static', // static methods
                'method_protected_static',
                'method_private_static',

                'method_public', // methods
                'method_protected',
                'method_private',

                'phpunit', // PHPUnit
            ],
            'sort_algorithm' => 'none',
        ],
        'single_line_comment_style' => false,
        'visibility_required' => [
            'elements' => ['property', 'method', 'const'],
        ],
        'native_function_invocation' => true,
        'native_constant_invocation' => true,
        'phpdoc_summary' => false,
        'return_assignment' => false,
        'phpdoc_align' => false,
        'phpdoc_to_comment' => false,
        'php_unit_test_class_requires_covers' => false,
    ])
    ->setFinder($finder)
;
