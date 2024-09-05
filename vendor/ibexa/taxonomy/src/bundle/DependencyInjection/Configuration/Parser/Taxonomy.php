<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Configuration parser for pagination limits declaration.
 *
 * Example configuration:
 * ```yaml
 * ibexa:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          taxonomy:
 *              pagination:
 *                  tab_assigned_content_limit: 10
 *              admin_ui:
 *                  delete_subtree_size_limit: 100
 * ```
 */
final class Taxonomy extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('taxonomy')
                ->info('Configuration for Ibexa Taxonomy feature')
                ->children()
                    ->arrayNode('pagination')
                        ->children()
                            ->scalarNode('tab_assigned_content_limit')->isRequired()->end()
                        ->end()
                    ->end()
                    ->arrayNode('admin_ui')
                        ->children()
                            ->scalarNode('delete_subtree_size_limit')
                                ->info(
                                    'Sets the limit on how many Taxonomy Entries can be removed at once. '
                                    . 'Lower this number if you are dealing with timeouts when removing large '
                                    . 'subtrees in Taxonomy tree.'
                                )
                                ->defaultValue(100)
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param array<string, mixed> $scopeSettings
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['taxonomy'])) {
            return;
        }

        $settings = $scopeSettings['taxonomy'];

        if (array_key_exists('pagination', $settings)) {
            $contextualizer->setContextualParameter(
                'taxonomy.pagination.tab_assigned_content_limit',
                $currentScope,
                $settings['pagination']['tab_assigned_content_limit']
            );
        }

        if (array_key_exists('admin_ui', $settings)) {
            $contextualizer->setContextualParameter(
                'taxonomy.admin_ui.delete_subtree_size_limit',
                $currentScope,
                $settings['admin_ui']['delete_subtree_size_limit']
            );
        }
    }
}
