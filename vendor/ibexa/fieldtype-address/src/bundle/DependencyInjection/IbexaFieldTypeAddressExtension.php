<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FieldTypeAddress\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\Yaml\Yaml;

final class IbexaFieldTypeAddressExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    protected function loadInternal(
        array $mergedConfig,
        ContainerBuilder $container
    ): void {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        if ($this->shouldLoadTestServices($container)) {
            $loader->load('test/components.yaml');
        }

        if (!empty($mergedConfig['formats'])) {
            $container->setParameter('ibexa.address.formats', $mergedConfig['formats']);
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependFormats($container);
        $this->prependCoreSettings($container);
        $this->prependJMSTranslation($container);
        $this->prependGraphQL($container);
    }

    private function prependFormats(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/formats.yaml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig($this->getAlias(), $config);
        $container->addResource(new FileResource($configFile));
    }

    private function prependCoreSettings(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/ibexa.yaml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_fieldtype_address' => [
                    'dirs' => [
                        __DIR__ . '/../../',
                    ],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'excluded_dirs' => ['Behat', 'Tests', 'node_modules'],
                    'output_format' => 'xliff',
                ],
            ],
        ]);
    }

    private function prependGraphQL(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('overblog_graphql', [
            'definitions' => [
                'mappings' => [
                    'types' => [
                        [
                            'type' => 'yaml',
                            'dir' => __DIR__ . '/../Resources/config/graphql/types',
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function shouldLoadTestServices(ContainerBuilder $container): bool
    {
        return $container->hasParameter('ibexa.behat.browser.enabled')
            && true === $container->getParameter('ibexa.behat.browser.enabled');
    }
}
