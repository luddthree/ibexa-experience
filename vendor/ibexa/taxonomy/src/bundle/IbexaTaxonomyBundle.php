<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy;

use Ibexa\Bundle\Taxonomy\DependencyInjection\Configuration\Parser\Taxonomy;
use Ibexa\Taxonomy\Security\TaxonomyPolicyProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaTaxonomyBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $core */
        $core = $container->getExtension('ibexa');
        $core->addConfigParser(new Taxonomy());

        $core->addDefaultSettings(__DIR__ . '/Resources/config', ['ibexa_default_settings.yaml']);
        $core->addPolicyProvider(new TaxonomyPolicyProvider());

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

        if ($container->hasExtension('ibexa_cdp')) {
            $loader->load('services/cdp.yaml');
        }
    }
}
