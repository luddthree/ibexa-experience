<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Seo\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * @internal
 */
final class SeoConfigParser extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('seo')
                ->children()
                    ->arrayNode('types')
                        ->useAttributeAsKey('type')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param array<string, array<string, mixed>> $scopeSettings
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['seo'])) {
            return;
        }

        $settings = $scopeSettings['seo'];

        if (!empty($settings['types'])) {
            $scopeSettings['seo.types'] = $settings['types'];
        }
    }

    /**
     * @param array<string, mixed> $config
     */
    public function postMap(array $config, ContextualizerInterface $contextualizer)
    {
        $contextualizer->mapConfigArray(
            'seo.types',
            $config
        );
    }
}
