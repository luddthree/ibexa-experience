<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Measurement\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @internal
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ibexa_measurement');

        $treeBuilder
            ->getRootNode()
            ->children()
                ->arrayNode('conversion')
                    ->children()
                        ->arrayNode('formulas')
                            ->info('An array of formulas to add to built-in ones.')
                            ->fixXmlConfig('formula')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('source_unit')
                                        ->info('Should match one of types in "ibexa_measurement.types" or built-in types.')
                                        ->example([
                                            'meter',
                                            'acre',
                                        ])
                                        ->isRequired()
                                    ->end()
                                    ->scalarNode('target_unit')
                                        ->info('Should match one of types in "ibexa_measurement.types" or built-in types.')
                                        ->example([
                                            'meter',
                                            'acre',
                                        ])
                                        ->isRequired()
                                    ->end()
                                    ->scalarNode('formula')
                                        ->info('Formula used to perform conversion. "value" variable will contain source value')
                                        ->example([
                                            'value / 1000',
                                            'value * 0.4',
                                        ])
                                        ->isRequired()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('types')
                    ->useAttributeAsKey('type')
                    ->arrayPrototype()
                        ->useAttributeAsKey('unit')
                        ->arrayPrototype() // each type has multiple units
                            ->children()
                                ->scalarNode('symbol')->end()
                                ->booleanNode('is_base_unit')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
