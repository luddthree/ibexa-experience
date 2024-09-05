<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison;

use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

trait MatrixFieldTypeSetupFactoryTrait
{
    protected function loadMatrixFieldTypeSettings(ContainerBuilder $containerBuilder): void
    {
        $configPath = realpath(__DIR__ . '/../../vendor/ibexa/fieldtype-matrix/src/bundle/Resources/config/');
        if (false === $configPath) {
            throw new RuntimeException('Unable to find IbexaFieldTypeMatrixBundle package config');
        }

        $loader = new YamlFileLoader($containerBuilder, new FileLocator($configPath));
        $loader->load('services/fieldtype.yaml');
    }
}

class_alias(MatrixFieldTypeSetupFactoryTrait::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\MatrixFieldTypeSetupFactoryTrait');
