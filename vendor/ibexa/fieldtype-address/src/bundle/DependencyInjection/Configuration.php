<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FieldTypeAddress\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ibexa_address');

        $treeBuilder
            ->getRootNode()
                ->children()
                    ->arrayNode('formats')
                        ->useAttributeAsKey('identifier')
                        ->arrayPrototype()
                        ->children()
                            ->arrayNode('country')
                                ->useAttributeAsKey('identifier')
                                ->arrayPrototype()
                                ->scalarPrototype()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
