<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\OAuth2Client\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ParserInterface;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Siteaccess-aware OAuth2 configuration.
 *
 * Example configuration:
 *
 *  oauth2:
 *      enabled: true
 *      clients: ['facebook', 'azure']
 */
final class OAuth2Parser implements ParserInterface
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('oauth2')
                ->info('OAuth2 configuration')
                ->children()
                    ->booleanNode('enabled')
                        ->defaultFalse()
                    ->end()
                    ->arrayNode('clients')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(
        array &$scopeSettings,
        $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        if (empty($scopeSettings['oauth2'])) {
            return;
        }

        $settings = $scopeSettings['oauth2'];

        if (isset($settings['enabled'])) {
            $contextualizer->setContextualParameter(
                'oauth2.enabled',
                $currentScope,
                $settings['enabled']
            );
        }

        if (isset($settings['clients'])) {
            $contextualizer->setContextualParameter(
                'oauth2.clients',
                $currentScope,
                $settings['clients']
            );
        }
    }

    public function preMap(array $config, ContextualizerInterface $contextualizer): void
    {
    }

    public function postMap(array $config, ContextualizerInterface $contextualizer): void
    {
    }
}

class_alias(OAuth2Parser::class, 'Ibexa\Platform\Bundle\OAuth2Client\DependencyInjection\Configuration\Parser\OAuth2Parser');
