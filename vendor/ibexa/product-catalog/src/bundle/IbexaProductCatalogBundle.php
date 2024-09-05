<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog;

use Ibexa\Bundle\ProductCatalog\DependencyInjection\Compiler\AttributeStorageGatewayCompilerPass;
use Ibexa\Bundle\ProductCatalog\DependencyInjection\Configuration\RepositoryAware\ProductCatalogParser as RepositoryAwareProductCatalogParser;
use Ibexa\Bundle\ProductCatalog\DependencyInjection\Configuration\SiteAccessAware\ProductCatalogParser as SiteaccessAwareConfigurationParser;
use Ibexa\ProductCatalog\Security\ProductCatalogPolicyProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaProductCatalogBundle extends Bundle
{
    private bool $forceLoadingCommerceBridge;

    public function __construct(bool $forceLoadingCommerceBridge = false)
    {
        $this->forceLoadingCommerceBridge = $forceLoadingCommerceBridge;
    }

    public function build(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/Resources/config')
        );

        if ($container->hasExtension('ibexa_solr')) {
            $loader->load('services/search/solr.yaml');
        }

        if ($container->hasExtension('ibexa_elasticsearch')) {
            $loader->load('services/search/elasticsearch.yaml');
        }

        if ($container->hasExtension('ibexa_admin_ui')) {
            $loader->load('services/limitations.yaml');
        }

        if ($container->hasExtension('ibexa_personalization')) {
            $loader->load('services/personalization/storages.yaml');
            $loader->load('services/personalization/services.yaml');
            $loader->load('services/personalization/event_subscribers.yaml');
        }

        if ($this->shouldLoadCommerceBridge($container)) {
            $loader->load('services/bridge.yaml');
        }

        if ($container->hasExtension('ibexa_field_type_matrix')) {
            $loader->load('services/field_type_matrix/field_type.yaml');
        }

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernel */
        $kernel = $container->getExtension('ibexa');
        $kernel->addConfigParser(new SiteaccessAwareConfigurationParser());
        $kernel->addRepositoryConfigParser(new RepositoryAwareProductCatalogParser());
        $kernel->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);
        $kernel->addPolicyProvider(new ProductCatalogPolicyProvider());

        $container->addCompilerPass(new AttributeStorageGatewayCompilerPass());
    }

    private function shouldLoadCommerceBridge(ContainerBuilder $container): bool
    {
        if ($this->forceLoadingCommerceBridge) {
            return true;
        }

        $dependencies = [
            'ibexa_commerce_eshop',
            'ibexa_commerce_price',
            'ibexa_commerce_checkout',
            'ibexa_commerce_local_order_management',
        ];

        foreach ($dependencies as $dependency) {
            if (!$container->hasExtension($dependency)) {
                return false;
            }
        }

        return true;
    }
}
