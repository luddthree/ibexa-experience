<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison;

use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PoliciesConfigBuilder;
use Ibexa\Bundle\VersionComparison\DependencyInjection\Configuration;
use Ibexa\Contracts\Core\Test\Repository\SetupFactory\Legacy as CoreLegacySetupFactory;
use Ibexa\Core\Base\ServiceContainer;
use Ibexa\VersionComparison\Security\ComparisonPolicyProvider;
use RuntimeException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class LegacySetupFactory extends CoreLegacySetupFactory
{
    use CoreSetupFactoryTrait;
    use MatrixFieldTypeSetupFactoryTrait;

    /**
     * Returns the service container used for initialization of the repository.
     *
     * @return \Ibexa\Core\Base\ServiceContainer
     *
     * @throws \Exception
     */
    public function getServiceContainer()
    {
        if (!isset(self::$serviceContainer)) {
            /** @var \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder */
            $containerBuilder = new ContainerBuilder();

            $this->externalBuildContainer($containerBuilder);

            self::$serviceContainer = new ServiceContainer(
                $containerBuilder,
                __DIR__,
                'var/cache',
                true,
                true
            );
        }

        return self::$serviceContainer;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     *
     * @throws \Exception
     */
    protected function externalBuildContainer(ContainerBuilder $containerBuilder)
    {
        parent::externalBuildContainer($containerBuilder);

        $this->loadCoreSettings($containerBuilder);
        $this->loadVersionComparisonSettings($containerBuilder);
        $this->loadMatrixFieldTypeSettings($containerBuilder);
    }

    private function loadVersionComparisonSettings(ContainerBuilder $containerBuilder)
    {
        $configPath = realpath(__DIR__ . '/../../src/bundle/Resources/config/');
        if (false === $configPath) {
            throw new RuntimeException('Unable to find Content Comparison package config');
        }

        $loader = new YamlFileLoader($containerBuilder, new FileLocator($configPath));
        $loader->load('services.yaml');
        $loader->load('comparable_fieldtypes.yaml');

        // load test settings
        $loader = new YamlFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__ . '/_fixtures/config')
        );
        $loader->load('services.yaml');
        $loader->load('parameters.yaml');

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, []);

        $this->setHtmlComparisonDefaultValueParameters($containerBuilder, $config['html']);
        $this->buildPolicyMap($containerBuilder);
    }

    protected function setHtmlComparisonDefaultValueParameters(
        ContainerBuilder $container,
        array $htmlComparisonConfig
    ): void {
        foreach ($htmlComparisonConfig as $key => $value) {
            $container->setParameter('ezplatform.version_comparison.html.' . $key, $value);
        }
    }

    private function buildPolicyMap(ContainerBuilder $container): void
    {
        $policiesBuilder = new PoliciesConfigBuilder($container);
        $provider = new ComparisonPolicyProvider();
        $provider->addPolicies($policiesBuilder);
    }
}

class_alias(LegacySetupFactory::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\LegacySetupFactory');
