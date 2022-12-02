<?php declare(strict_types=1);

$vendor = [
    'plugin/sdk',
    'plugin/vendor/doctrine/dbal/lib',
    'plugin/vendor/guzzlehttp/guzzle/src',
    'plugin/vendor/nyholm/psr7',
    'plugin/vendor/psr/http-message',
    'plugin/vendor/psr/log',
    'plugin/vendor/shopware/core',
    'plugin/vendor/symfony/cache-contracts',
    'plugin/vendor/symfony/console',
    'plugin/vendor/symfony/dependency-injection',
    'plugin/vendor/symfony/event-dispatcher',
    'plugin/vendor/symfony/framework-bundle',
    'plugin/vendor/symfony/http-foundation',
    'plugin/vendor/symfony/http-kernel',
    'plugin/vendor/symfony/messenger',
    'plugin/vendor/symfony/routing',
];

return [
    'target_php_version' => '7.4',
    'directory_list' => ['plugin/src', ...$vendor],
    'exclude_analysis_directory_list' => $vendor,
    'exclude_file_regex' => '~
        ^plugin/src/Command/TestCommand\.php$ |
        ^plugin/sdk/vendor/
    ~x',
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
