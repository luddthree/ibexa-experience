<?php

use Ibexa\CodeStyle\PhpCsFixer\InternalConfigFactory;

$configFactory = new InternalConfigFactory();
$configFactory->withRules([
    'declare_strict_types' => false,
]);

return $configFactory
    ->buildConfig()
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude([
                'vendor', 'Features', 'grunt',
            ])
            ->files()->name('*.php')
    );
