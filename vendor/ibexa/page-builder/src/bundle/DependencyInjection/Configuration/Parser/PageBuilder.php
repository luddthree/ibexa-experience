<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Configuration parser for Page Builder.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          page_builder:
 *              siteaccess_list: [site, de, fr, no]
 *              siteaccess_hosts:
 *                  - some.site.com
 *                  - another.some.site.com
 *              inject_cross_origin_helper: true
 * ```
 */
class PageBuilder extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('page_builder')
                ->info('Page Builder configuration')
                ->children()
                    ->arrayNode('siteaccess_list')
                        ->scalarPrototype()->end()
                        ->info('List of siteaccesses available for content preview in Page Builder.')
                        ->defaultValue([])
                    ->end()
                    ->arrayNode('siteaccess_hosts')
                        ->scalarPrototype()->end()
                        ->info('List of siteaccesses hosts for content preview in Page Builder.')
                        ->defaultValue([])
                    ->end()
                    ->booleanNode('inject_cross_origin_helper')
                        ->defaultTrue()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['page_builder'])) {
            return;
        }

        $settings = $scopeSettings['page_builder'];

        if (!empty($settings['siteaccess_list'])) {
            $contextualizer->setContextualParameter(
                'page_builder.siteaccess_list',
                $currentScope,
                $settings['siteaccess_list']
            );
        }

        if (!empty($settings['siteaccess_hosts'])) {
            $contextualizer->setContextualParameter(
                'page_builder.siteaccess_hosts',
                $currentScope,
                $settings['siteaccess_hosts']
            );
        }

        if (!empty($settings['inject_cross_origin_helper'])) {
            $contextualizer->setContextualParameter(
                'page_builder.inject_cross_origin_helper',
                $currentScope,
                $settings['inject_cross_origin_helper']
            );
        }

        if (!empty($settings['timeline']['date_format']['event_date'])) {
            $contextualizer->setContextualParameter(
                'page_builder.timeline.date_format.event_date',
                $currentScope,
                $settings['timeline']['date_format']['event_date']
            );
        }

        if (!empty($settings['timeline']['date_format']['time_marker'])) {
            $contextualizer->setContextualParameter(
                'page_builder.timeline.date_format.time_marker',
                $currentScope,
                $settings['timeline']['date_format']['time_marker']
            );
        }

        if (!empty($settings['timeline']['timezone'])) {
            $contextualizer->setContextualParameter(
                'page_builder.timeline.timezone',
                $currentScope,
                $settings['timeline']['timezone']
            );
        }
    }
}

class_alias(PageBuilder::class, 'EzSystems\EzPlatformPageBuilderBundle\DependencyInjection\Configuration\Parser\PageBuilder');
