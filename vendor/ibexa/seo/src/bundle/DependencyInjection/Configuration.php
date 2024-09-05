<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Seo\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @internal
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ibexa_seo');

        $treeBuilder
            ->getRootNode()
            ->children()
                ->arrayNode('types')
                    ->useAttributeAsKey('type')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('label')->end()
                            ->scalarNode('template')->defaultNull()->end()
                            ->arrayNode('fields')
                            ->useAttributeAsKey('fields')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('label')->isRequired()->end()
                                        ->scalarNode('type')->defaultValue('text')->end()
                                        ->scalarNode('key')->defaultNull()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
