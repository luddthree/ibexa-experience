<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Fastly\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class FastlyConfigParser extends AbstractParser
{
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (isset($scopeSettings['fastly']['service_id'])) {
            $contextualizer->setContextualParameter(
                'http_cache.fastly.service_id',
                $currentScope,
                $scopeSettings['fastly']['service_id']
            );
        }
        if (isset($scopeSettings['fastly']['key'])) {
            $contextualizer->setContextualParameter(
                'http_cache.fastly.key',
                $currentScope,
                $scopeSettings['fastly']['key']
            );
        }
    }

    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('fastly')
                ->isRequired()
                ->children()
                    ->scalarNode('service_id')
                        ->info('Fastly service id')
                        ->isRequired()
                    ->end()
                    ->scalarNode('key')
                        ->info('Fastly auth token. Must have "purge_select" scope')
                        ->isRequired()
                    ->end()
                ->end()
            ->end()
        ;
    }
}

class_alias(FastlyConfigParser::class, 'EzSystems\PlatformFastlyCacheBundle\DependencyInjection\FastlyConfigParser');
