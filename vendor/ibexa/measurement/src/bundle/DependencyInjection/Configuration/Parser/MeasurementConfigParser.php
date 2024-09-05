<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Measurement\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * @internal
 */
class MeasurementConfigParser extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('measurement')
                ->children()
                    ->arrayNode('types')
                        ->prototype('array')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param array<string, array<string, mixed>> $scopeSettings
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['measurement'])) {
            return;
        }

        $settings = $scopeSettings['measurement'];

        if (!empty($settings['types'])) {
            $scopeSettings['measurement.types'] = $settings['types'];
        }
    }

    /**
     * @param array<string, mixed> $config
     */
    public function postMap(array $config, ContextualizerInterface $contextualizer)
    {
        $contextualizer->mapConfigArray(
            'measurement.types',
            $config
        );
    }
}
