<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\DependencyInjection;

use Ibexa\Contracts\PageBuilder\Timeline\EventInterface;
use Ibexa\PageBuilder\PageBuilder\Timeline\BasicEvent;
use Ibexa\PageBuilder\PageBuilder\Timeline\RecurringEvent;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Validates and merges configuration from app/config files.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(IbexaPageBuilderExtension::EXTENSION_NAME);

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('timeline')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('serializer')
                            ->info('Configuration for Timeline events serializer.')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('metadata_dirs')
                                    ->info(
                                        'Namespace prefix and directory map containing YAML definitions for JMS Serializer. '
                                        . 'Reference to: https://jmsyst.com/libs/serializer/master/reference/yml_reference'
                                    )
                                    ->example([
                                        'Ibexa\\PageBuilder' => '@IbexaBuilderBundle/Resources/config/serializer',
                                    ])
                                    ->defaultValue([])
                                    ->useAttributeAsKey('namespace_prefix')
                                    ->scalarPrototype()->end()
                                ->end()
                                ->arrayNode('event_type_map')
                                    ->info(sprintf(
                                        'Map of event types and classes. The type has to match the %s::getType value.',
                                        EventInterface::class
                                    ))
                                    ->example([
                                        'custom_event_type' => BasicEvent::class,
                                        'recurring' => RecurringEvent::class,
                                    ])
                                    ->defaultValue([])
                                    ->useAttributeAsKey('event_type')
                                    ->scalarPrototype()->end()
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

class_alias(Configuration::class, 'EzSystems\EzPlatformPageBuilderBundle\DependencyInjection\Configuration');
