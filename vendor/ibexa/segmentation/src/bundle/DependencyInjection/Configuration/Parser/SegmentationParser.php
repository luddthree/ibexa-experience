<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class SegmentationParser extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('segmentation')
                ->info('Segmentation configuration')
                    ->children()
                        ->arrayNode('pagination')
                            ->info('Pagination configuration.')
                            ->children()
                                ->scalarNode('segment_groups_limit')->defaultValue(10)->end()
                                ->scalarNode('user_view_segments_limit')->defaultValue(10)->end()
                            ->end()
                        ->end()
                        ->arrayNode('segment_groups')
                            ->info('Segment Groups configuration')
                            ->children()
                                ->arrayNode('list')
                                    ->arrayPrototype()
                                        ->children()
                                            ->booleanNode('protected')
                                                ->info('Set this group as protected')
                                                ->defaultFalse()
                                                ->isRequired()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (!empty($scopeSettings['segmentation'])) {
            $settings = $scopeSettings['segmentation'];

            if (!empty($settings['pagination']['segment_groups_limit'])) {
                $contextualizer->setContextualParameter(
                    'segmentation.pagination.segment_groups_limit',
                    $currentScope,
                    $settings['pagination']['segment_groups_limit']
                );
            }

            if (!empty($settings['pagination']['user_view_segments_limit'])) {
                $contextualizer->setContextualParameter(
                    'segmentation.pagination.user_view_segments_limit',
                    $currentScope,
                    $settings['pagination']['user_view_segments_limit']
                );
            }

            if (!empty($settings['segment_groups']['list'])) {
                $contextualizer->setContextualParameter(
                    'segmentation.segment_groups.list',
                    $currentScope,
                    $settings['segment_groups']['list']
                );
            }
        }
    }
}

class_alias(SegmentationParser::class, 'Ibexa\Platform\Bundle\Segmentation\DependencyInjection\Configuration\Parser\SegmentationParser');
