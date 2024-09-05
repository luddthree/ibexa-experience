<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\DependencyInjection;

use Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\DataToStructTransformerInterface;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\IndexDataProviderInterface;
use Ibexa\Contracts\ProductCatalog\NameSchema\NameSchemaStrategyInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityStrategyInterface;
use Ibexa\Contracts\ProductCatalog\QueryTypeInterface;
use Ibexa\Contracts\ProductCatalog\REST\Output\AttributePostProcessorInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\DomainMapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\MapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\PersisterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\Yaml\Yaml;

final class IbexaProductCatalogExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    /**
     * @param array<string, mixed> $mergedConfig
     *
     * @throws \Exception
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $container->setParameter(
            'ibexa.product_catalog.engines',
            $mergedConfig['engines'] ?? []
        );

        $loader = $this->getFileLoader($container);
        $loader->load('services.yaml');

        if ($this->shouldLoadTestServices($container)) {
            $loader->load('test/components.yaml');
            $loader->load('test/feature_contexts.yaml');
            $loader->load('test/pages.yaml');
        }

        $this->registerForAutoConfiguration($container);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/prepend.yaml';

        $container->addResource(new FileResource($configFile));
        foreach (Yaml::parseFile($configFile, Yaml::PARSE_CONSTANT) as $name => $config) {
            $container->prependExtensionConfig($name, $config);
        }

        $this->prependJMSTranslation($container);

        if ($container->hasExtension('ibexa_admin_ui')) {
            $this->prependDefaultSettings($container);
        }

        $this->prependGraphQL($container);

        if ($container->hasExtension('ibexa_fieldtype_page')) {
            $this->prependPageBuilderConfiguration($container);

            $loader = $this->getFileLoader($container);
            $loader->load('services/page_builder.yaml');
        }

        if ($container->hasExtension('ibexa_dashboard')) {
            $loader = $this->getFileLoader($container);
            $loader->load('services/dashboard_installer.yaml');
        }
    }

    private function getFileLoader(ContainerBuilder $container): FileLoader
    {
        return new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_product_catalog' => [
                    'dirs' => [
                        __DIR__ . '/../../',
                    ],
                    'excluded_dirs' => ['Behat', 'Bridge'],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
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

    private function prependDefaultSettings(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/defaults/templates.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

    private function prependPageBuilderConfiguration(ContainerBuilder $container): void
    {
        $this->prependPageBlock($container);
        $this->prependPageBuilderFormTemplateSettings($container);
    }

    private function prependPageBlock(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/page_builder/blocks.yaml';
        $blockConfig = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('ibexa_fieldtype_page', $blockConfig);
        $container->addResource(new FileResource($configFile));
    }

    private function prependPageBuilderFormTemplateSettings(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/page_builder/form_templates.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

    private function registerForAutoConfiguration(ContainerBuilder $container): void
    {
        $this->registerProductPriceInterfacesForAutoConfiguration($container);

        $container->registerForAutoconfiguration(ContextProviderInterface::class)
            ->addTag('ibexa.product_catalog.permission.context_provider');

        $container->registerForAutoconfiguration(ProductAvailabilityStrategyInterface::class)
            ->addTag('ibexa.product_catalog.availability.strategy');

        $container->registerForAutoconfiguration(QueryTypeInterface::class)
            ->addTag('ibexa.product_catalog.query_type');

        $container->registerForAutoconfiguration(IndexDataProviderInterface::class)
            ->addTag('ibexa.product_catalog.attribute.index_data_provider');

        $container->registerForAutoconfiguration(AttributePostProcessorInterface::class)
            ->addTag('ibexa.product_catalog.rest.output.attribute.post_processor');

        $container->registerForAutoconfiguration(NameSchemaStrategyInterface::class)
            ->addTag('ibexa.product_catalog.naming_schema_strategy');
    }

    private function registerProductPriceInterfacesForAutoConfiguration(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(DataToStructTransformerInterface::class)
            ->addTag('ibexa.product_catalog.price.data_to_struct_transformer');

        $container->registerForAutoconfiguration(MapperInterface::class)
            ->addTag('ibexa.product_catalog.product_price.inheritance.mapper');

        $container->registerForAutoconfiguration(DomainMapperInterface::class)
            ->addTag('ibexa.product_catalog.product_price.inheritance.domain_mapper');

        $container->registerForAutoconfiguration(PersisterInterface::class)
            ->addTag('ibexa.product_catalog.product_price.inheritance.persister');
    }

    private function shouldLoadTestServices(ContainerBuilder $container): bool
    {
        return $container->hasParameter('ibexa.behat.browser.enabled')
            && true === $container->getParameter('ibexa.behat.browser.enabled');
    }
}
