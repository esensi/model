<?php

namespace PhpCsFixer;

$cacheDir = getenv('TRAVIS') ? getenv('HOME').'/.php-cs-fixer' : __DIR__;

return Config::create()
    ->setUsingCache(true)
    ->setCacheFile($cacheDir.'/.php_cs.cache')
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'not_operator_with_space' => true,
        'ordered_imports' => true,
        'phpdoc_order' => true,
        'phpdoc_type_to_var' => true,
        'psr0' => false,
        'short_array_syntax' => true,
        'unalign_double_arrow' => false,
        'unalign_equals' => false,
    ])
    ->finder(
        Finder::create()
            ->in(__DIR__.'/src')
            ->in(__DIR__.'/tests')
    );
