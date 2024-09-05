<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

final class CorporateAccounts extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('corporate_accounts')
                ->info('Corporate accounts related configuration')
                ->children()
                    ->arrayNode('roles')
                        ->info('Roles identifiers used for corporate accounts. Case sensitive')
                        ->ignoreExtraKeys(false)
                        ->scalarPrototype()->end()
                    ->end()
                    ->arrayNode('reasons')
                        ->info('Predefined set of application stage reasons.')
                        ->prototype('array')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                    ->arrayNode('application')
                        ->children()
                            ->arrayNode('states')
                                ->info('States identifiers used for corporate account applications. Case sensitive')
                                ->ignoreExtraKeys(false)
                                ->scalarPrototype()->end()
                            ->end()
                            ->scalarNode('default_state')
                                ->info('State used for new corporate account applications. Case sensitive')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;
    }

    /**
     * @param array<string, mixed> $scopeSettings
     * @param string $currentScope
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (!empty($scopeSettings['corporate_accounts'])) {
            $settings = $scopeSettings['corporate_accounts'];
            if (!empty($settings['roles'])) {
                $contextualizer->setContextualParameter(
                    'corporate_accounts.roles',
                    $currentScope,
                    $settings['roles']
                );
            }

            if (!empty($settings['reasons'])) {
                $contextualizer->setContextualParameter(
                    'corporate_accounts.reasons',
                    $currentScope,
                    $settings['reasons']
                );
            }

            if (!empty($settings['applications'])) {
                $contextualizer->setContextualParameter(
                    'corporate_accounts.applications.states',
                    $currentScope,
                    $settings['applications']['states']
                );
                $contextualizer->setContextualParameter(
                    'corporate_accounts.applications.default_state',
                    $currentScope,
                    $settings['applications']['default_state']
                );
            }
        }
    }
}
