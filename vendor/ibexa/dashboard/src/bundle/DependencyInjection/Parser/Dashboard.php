<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\DependencyInjection\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Configuration parser for Dashboard.
 *
 * Example configuration:
 * ```yaml
 * ibexa:
 *   system:
 *      admin_group: # configuration per SiteAccess or SiteAccess group
 *          dashboard:
 *              container_remote_id: remote_id
 *              default_dashboard_remote_id: remote_id
 *              users_container_remote_id: remote_id
 *              predefined_container_remote_id: remote_id
 *              section_identifier: dashboard
 *              content_type_identifier: dashboard_landing_page
 *              container_content_type_identifier: folder
 * ```
 */
final class Dashboard extends AbstractParser
{
    public const DASHBOARD_CONTAINER_REMOTE_ID = 'dashboard_container';
    public const DEFAULT_DASHBOARD_REMOTE_ID = 'default_dashboard';
    public const USER_DASHBOARDS_CONTAINER_REMOTE_ID = 'user_dashboards';
    public const PREDEFINED_DASHBOARDS_CONTAINER_REMOTE_ID = 'predefined_dashboards';
    public const DASHBOARD_SECTION_IDENTIFIER = 'dashboard';
    public const DASHBOARD_CONTENT_TYPE_IDENTIFIER = 'dashboard_landing_page';
    public const DASHBOARD_CONTENT_TYPE_GROUP_IDENTIFIER = 'Dashboard';
    public const DASHBOARD_CONTAINER_CONTENT_TYPE_IDENTIFIER = 'folder';

    private const KEYS = [
        'container_remote_id',
        'default_dashboard_remote_id',
        'users_container_remote_id',
        'predefined_container_remote_id',
        'section_identifier',
        'content_type_identifier',
        'content_type_group_identifier',
        'container_content_type_identifier',
    ];

    /**
     * {@inheritdoc}
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('dashboard')
                ->info('Dashboard configuration')
                ->children()
                    ->scalarNode('container_remote_id')
                        ->info('Root Location remote ID for dashboards')
                        ->defaultValue(self::DASHBOARD_CONTAINER_REMOTE_ID)
                    ->end()
                    ->scalarNode('default_dashboard_remote_id')
                        ->info('Location remote ID of default dashboard')
                        ->defaultValue(self::DEFAULT_DASHBOARD_REMOTE_ID)
                    ->end()
                    ->scalarNode('users_container_remote_id')
                        ->info('Location remote ID of a container of all users custom dashboards')
                        ->defaultValue(self::USER_DASHBOARDS_CONTAINER_REMOTE_ID)
                    ->end()
                    ->scalarNode('predefined_container_remote_id')
                        ->info('Location remote ID of a container of all predefined dashboards')
                        ->defaultValue(self::PREDEFINED_DASHBOARDS_CONTAINER_REMOTE_ID)
                    ->end()
                    ->scalarNode('section_identifier')
                        ->info('Section identifier of dashboards')
                        ->defaultValue(self::DASHBOARD_SECTION_IDENTIFIER)
                    ->end()
                    ->scalarNode('content_type_identifier')
                        ->info('Content type identifier of dashboards')
                        ->defaultValue(self::DASHBOARD_CONTENT_TYPE_IDENTIFIER)
                    ->end()
                    ->scalarNode('content_type_group_identifier')
                        ->info('Content type group identifier of dashboard content types')
                        ->defaultValue(self::DASHBOARD_CONTENT_TYPE_GROUP_IDENTIFIER)
                    ->end()
                    ->scalarNode('container_content_type_identifier')
                        ->info('Content type identifier of dashboard container')
                        ->defaultValue(self::DASHBOARD_CONTAINER_CONTENT_TYPE_IDENTIFIER)
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param array<string,mixed> $scopeSettings
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        $settings = $scopeSettings['dashboard'] ?? [];

        if (empty($settings)) {
            return;
        }

        foreach (self::KEYS as $key) {
            if (empty($settings[$key])) {
                continue;
            }

            $contextualizer->setContextualParameter(
                sprintf('dashboard.%s', $key),
                $currentScope,
                $settings[$key]
            );
        }
    }
}
