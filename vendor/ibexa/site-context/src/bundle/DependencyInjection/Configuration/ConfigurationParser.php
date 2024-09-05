<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\DependencyInjection\Configuration;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

final class ConfigurationParser extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('site_context')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('excluded_paths')
                        ->scalarPrototype()
                            ->defaultValue([])
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param array<string, mixed> $scopeSettings
     * @param string $currentScope
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (isset($scopeSettings['site_context']['excluded_paths'])) {
            $contextualizer->setContextualParameter(
                'site_context.excluded_paths',
                $currentScope,
                $scopeSettings['site_context']['excluded_paths']
            );
        }
    }
}
