<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    private const TREE_ROOT = 'ibexa_product_catalog';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder(self::TREE_ROOT);

        $rootNode = $builder->getRootNode();
        $rootNode
            ->fixXmlConfig('engine')
            ->children()
                ->arrayNode('engines')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('type')
                                ->defaultValue(null)
                            ->end()
                            ->variableNode('options')
                                ->defaultValue([])
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}
