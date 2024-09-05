<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(IbexaConnectExtension::EXTENSION_NAME);

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('scenario_block')
                    ->children()
                        ->arrayNode('block_templates')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('label')
                                        ->info('Label presented in Ibexa Connect. Key will be used if not set')
                                    ->end()
                                    ->scalarNode('template')
                                        ->isRequired()
                                        ->info('Template to be used in this block')
                                        ->example('@ibexa_design/foo/bar.html.twig')
                                    ->end()
                                    ->arrayNode('parameters')
                                        ->arrayPrototype()
                                            ->info('Parameters that Ibexa Connect should present to user in scenarios')
                                            ->example([
                                                ['label' => 'Foo', 'type' => 'string', 'required' => true],
                                                ['type' => 'string', 'required' => true],
                                                ['type' => 'string', 'required' => false],
                                                'foo',
                                            ])
                                            ->beforeNormalization()
                                                ->ifString()
                                                ->then(static fn (string $value): array => ['type' => $value])
                                            ->end()
                                            ->children()
                                                ->scalarNode('label')
                                                    ->defaultNull()
                                                ->end()
                                                ->scalarNode('type')
                                                    ->isRequired()
                                                ->end()
                                                ->scalarNode('required')
                                                    ->defaultFalse()
                                                ->end()
                                            ->end()
                                            ->ignoreExtraKeys(false)
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
