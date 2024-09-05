<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @deprecated since Ibexa 4.0, use
     * {@see \Ibexa\Bundle\SiteFactory\DependencyInjection\IbexaSiteFactoryExtension::EXTENSION_NAME}
     * instead.
     */
    public const TREE_ROOT = IbexaSiteFactoryExtension::EXTENSION_NAME;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(IbexaSiteFactoryExtension::EXTENSION_NAME);

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->booleanNode('enabled')
                    ->defaultValue(false)
                ->end()
                ->arrayNode('update_roles')
                    ->info(
                        'List of Role identifiers that will be granted user/login access to the new Site'
                    )
                    ->example([
                        'Anonymous',
                        'Administrator',
                    ])
                    ->defaultValue([])
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('templates')
                    ->useAttributeAsKey('identifier')
                    ->prototype('array')
                        ->children()
                        ->scalarNode('siteaccess_group')
                            ->isRequired()
                        ->end()
                        ->scalarNode('name')
                            ->isRequired()
                        ->end()
                        ->scalarNode('thumbnail')
                            ->isRequired()
                        ->end()
                        ->scalarNode('site_skeleton_id')
                            ->info('This value should be ID of Location.')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('site_skeleton_remote_id')
                            ->info('This value should be remote ID of Location.')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('parent_location_id')
                            ->info('This value should be ID of Location which will be parent of created Site.')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('parent_location_remote_id')
                            ->info('This value should be remote ID of Location which will be parent of created Site.')
                            ->defaultNull()
                        ->end()
                        ->arrayNode('user_group_skeleton_ids')
                            ->scalarPrototype()->end()
                            ->info('This value should be array of ID of User Group.')
                        ->end()
                        ->arrayNode('user_group_skeleton_remote_ids')
                            ->scalarPrototype()->end()
                            ->info('This value should be array of remote ID of User Group.')
                        ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

class_alias(Configuration::class, 'EzSystems\EzPlatformSiteFactoryBundle\DependencyInjection\Configuration');
