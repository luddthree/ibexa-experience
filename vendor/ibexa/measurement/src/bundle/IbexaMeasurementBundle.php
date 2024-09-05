<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Measurement;

use Ibexa\Bundle\Measurement\DependencyInjection\Configuration\Parser\MeasurementConfigParser;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaMeasurementBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $core */
        $core = $container->getExtension('ibexa');

        $core->addConfigParser(new MeasurementConfigParser());

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
            $loader->load('services/cdp/data_export.yaml');
        }
    }
}
