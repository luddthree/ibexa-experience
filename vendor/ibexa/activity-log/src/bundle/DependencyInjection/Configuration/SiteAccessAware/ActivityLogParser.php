<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\DependencyInjection\Configuration\SiteAccessAware;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query as ActivityLogQuery;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class ActivityLogParser extends AbstractParser
{
    /**
     * @param array<mixed> $scopeSettings
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (!isset($scopeSettings['activity_log'])) {
            return;
        }

        $settings = $scopeSettings['activity_log'];

        $this->addPaginationParameters($settings, $currentScope, $contextualizer);
    }

    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $rootActivityLogNode = $nodeBuilder->arrayNode('activity_log');

        $rootActivityLogNode->append($this->addPaginationConfiguration());
    }

    private function addPaginationConfiguration(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('pagination');
        $node = $treeBuilder->getRootNode();

        $node
            ->children()
                ->integerNode('activity_logs_limit')
                    ->isRequired()
                    ->defaultValue(ActivityLogQuery::DEFAULT_LIMIT)
                    ->min(1)
                ->end()
            ->end();

        return $node;
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function addPaginationParameters(
        array $settings,
        string $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        $names = [
            'activity_logs_limit',
        ];

        foreach ($names as $name) {
            if (isset($settings['pagination'][$name])) {
                $contextualizer->setContextualParameter(
                    'activity_log.pagination.' . $name,
                    $currentScope,
                    $settings['pagination'][$name]
                );
            }
        }
    }
}
