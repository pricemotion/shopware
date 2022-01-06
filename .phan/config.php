<?php

$vendor = [
    'shopware/vendor/doctrine/dbal/lib',
    'shopware/vendor/shopware/core',
    'shopware/vendor/symfony/console',
    'shopware/vendor/symfony/event-dispatcher',
    'shopware/vendor/symfony/framework-bundle',
    'shopware/vendor/symfony/http-foundation',
    'shopware/vendor/symfony/messenger',
    'shopware/vendor/symfony/routing',
];

return [
    'target_php_version' => '7.4',
    'directory_list' => ['plugin', ...$vendor],
    'exclude_analysis_directory_list' => $vendor,
    'plugins' => [
        'AlwaysReturnPlugin',
        'DollarDollarPlugin',
        'DuplicateArrayKeyPlugin',
        'DuplicateExpressionPlugin',
        'EmptyStatementListPlugin',
        'LoopVariableReusePlugin',
        'PregRegexCheckerPlugin',
        'PrintfCheckerPlugin',
        'SleepCheckerPlugin',
        'UnreachableCodePlugin',
        'UseReturnValuePlugin',
        'tools/phan/vendor/drenso/phan-extensions/Plugin/Annotation/SymfonyAnnotationPlugin.php',
    ],
];
