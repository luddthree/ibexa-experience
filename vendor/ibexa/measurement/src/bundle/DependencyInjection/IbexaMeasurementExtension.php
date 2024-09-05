<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Measurement\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\Yaml\Yaml;

/**
 * @internal
 */
final class IbexaMeasurementExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    /**
     * @phpstan-param array<string, array{
     *     conversion: array{
     *          formulas: mixed,
     *     },
     *     types: mixed,
     * }> $mergedConfig
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');
        $container->setParameter('measurement.types', $mergedConfig['types'] ?? []);
        $container->setParameter(
            'ibexa.measurement.value.converter.custom.formulas',
            $mergedConfig['conversion']['formulas'] ?? [],
        );
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependBuiltInUnitsConfig($container);
        $this->prependJMSTranslation($container);
        $this->prependBazingaJsTranslationConfiguration($container);
        $this->prependIbexaCoreConfiguration($container);
        $this->prependViews($container);
        $this->prependFormFields($container);
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_measurement' => [
                    'dirs' => [
                        __DIR__ . '/../../',
                    ],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                    'excluded_dirs' => ['CDP'],
                ],
            ],
        ]);
    }

    private function prependBazingaJsTranslationConfiguration(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/bazinga_js_translation.yaml';
        $config = Yaml::parseFile($configFile);
        /** @var array<string, mixed> $config */
        $container->prependExtensionConfig('bazinga_js_translation', $config);
        $container->addResource(new FileResource($configFile));
    }

    private function prependBuiltInUnitsConfig(ContainerBuilder $container): void
    {
        $builtInUnitsConfig = dirname(__DIR__) . '/Resources/config/builtin_units.yaml';
        $config = Yaml::parseFile($builtInUnitsConfig);
        /** @var array<string, mixed> $config */
        $container->prependExtensionConfig('ibexa_measurement', $config);
        $container->addResource(new FileResource($builtInUnitsConfig));
    }

    private function prependIbexaCoreConfiguration(ContainerBuilder $container): void
    {
        $coreExtensionConfigFile = dirname(__DIR__) . '/Resources/config/default_enabled_units.yaml';
        $config = Yaml::parseFile($coreExtensionConfigFile);
        /** @var array<string, mixed> $config */
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($coreExtensionConfigFile));
    }

    public function prependViews(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/prepend/views.yaml';
        /** @var array<string, mixed> $config */
        $config = Yaml::parse(self::loadFile($configFile));
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

    public function prependFormFields(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/prepend/form_fields.yaml';
        /** @var array<string, mixed> $config */
        $config = Yaml::parse(self::loadFile($configFile));
        $container->prependExtensionConfig('twig', $config);
        $container->addResource(new FileResource($configFile));
    }

    private static function loadFile(string $filePath): string
    {
        $contents = file_get_contents($filePath);
        if (false === $contents) {
            throw new InvalidArgumentException("Failed to load file contents of '$filePath'");
        }

        return $contents;
    }
}
