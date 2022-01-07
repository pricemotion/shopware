<?php

$vendor = [
    'shopware/vendor/doctrine/dbal/lib',
    'shopware/vendor/guzzlehttp/guzzle/src',
    'shopware/vendor/psr/http-message',
    'shopware/vendor/psr/log',
    'shopware/vendor/shopware/core',
    'shopware/vendor/symfony/console',
    'shopware/vendor/symfony/dependency-injection',
    'shopware/vendor/symfony/event-dispatcher',
    'shopware/vendor/symfony/framework-bundle',
    'shopware/vendor/symfony/http-client-contracts',
    'shopware/vendor/symfony/http-foundation',
    'shopware/vendor/symfony/messenger',
    'shopware/vendor/symfony/routing',
];

return [
    'target_php_version' => '7.4',
    'directory_list' => ['plugin', ...$vendor],
    'exclude_analysis_directory_list' => $vendor,
    'exclude_file_regex' => '~^plugin/src/Command/TestCommand\.php$~',
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
