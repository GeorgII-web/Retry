<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('public')
    ->exclude('app')
    ->exclude('var')
    ->exclude('node_modules')
    ->exclude('cache')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config->setUsingCache(true)
    ->setCacheFile(sprintf('%s/cache/.php-cs-fixer.cache', __DIR__))
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12'                                 => true,
        '@PHP56Migration:risky'                  => true,
        '@PHP70Migration'                        => true,
        '@PHP70Migration:risky'                  => true,
        '@PHP71Migration'                        => true,
        '@PHP71Migration:risky'                  => true,
        '@PHP73Migration'                        => true,
        '@PHP74Migration'                        => true,
        '@PHP74Migration:risky'                  => true,
        '@PHP80Migration'                        => true,
        '@PHP80Migration:risky'                  => true,
        '@PhpCsFixer'                            => true,
        '@PhpCsFixer:risky'                      => false,
        '@Symfony'                               => true,
        '@Symfony:risky'                         => true,
        '@DoctrineAnnotation'                    => true,
        'function_declaration'                   => ['closure_fn_spacing' => 'none'],
        'list_syntax'                            => ['syntax' => 'short'],
        'single_line_empty_body'                 => false,
        'strict_comparison'                      => false,
        'binary_operator_spaces'                 => [
            'operators' => [
                '='  => 'single_space',
                '=>' => 'single_space',
                '+=' => 'single_space',
                '-=' => 'single_space',
                '*=' => 'single_space',
                '%=' => 'single_space',
                '.=' => 'single_space',
                '^=' => 'single_space',
            ],
        ],
        'concat_space'                           => ['spacing' => 'one'],
        'yoda_style'                             => false,
        'class_definition'                       => [
            'single_line' => false,
        ],
        'native_function_invocation'             => true,
        'native_function_casing'                 => true,
        'native_constant_invocation'             => true,
        'object_operator_without_whitespace'     => false,
        'operator_linebreak'                     => true,
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'phpdoc_to_comment'                      => false,
        'php_unit_test_case_static_method_calls' => false,
        'global_namespace_import'                => [
            'import_classes'   => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'ordered_imports'                        => [
            'sort_algorithm' => 'alpha',
            'imports_order'  => [
                'const',
                'class',
                'function',
            ]
        ],
    ])
    ->setFinder($finder);
