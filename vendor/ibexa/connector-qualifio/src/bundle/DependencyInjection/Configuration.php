<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ConnectorQualifio\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ibexa_connector_qualifio');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->integerNode('client_id')
                    ->info('Qualifio Client id')
                ->end()
                ->scalarNode('channel')
                    ->info('Qualifio Channel identifier')
                ->end()
                ->scalarNode('feed_url')
                    ->info('Qualifio API endpoint uri')
                ->end()
                ->arrayNode('variable_map')
                    ->ignoreExtraKeys(false)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
