<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

use Ibexa\CodeStyle\PhpCsFixer\InternalConfigFactory;

$configFactory = new InternalConfigFactory();
$configFactory->withRules([
    'phpdoc_tag_type' => [
        'tags' => [
            'inheritdoc' => 'inline',
        ],
    ],
    'class_definition' => false,
]);
$config = $configFactory->buildConfig();

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude([
        'vendor',
    ])
    ->files()->name('*.php');

$config->setFinder($finder);

return $config;
