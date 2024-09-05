<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Workflow\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class WorkflowParser extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('workflows_config')
                ->info('Workflow configuration')
                ->children()
                    ->arrayNode('matcher_value_templates')
                        ->info('Matcher templates configuration.')
                        ->isRequired()
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('template')->end()
                                ->scalarNode('priority')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('timeline_entry_templates')
                        ->info('Timeline entries templates')
                        ->isRequired()
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('template')->end()
                                ->scalarNode('priority')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('pagination')
                        ->info('Pagination configuration.')
                        ->children()
                            ->scalarNode('workflow_limit')->isRequired()->end()
                            ->scalarNode('suggested_reviewers_limit')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('workflows')
                ->useAttributeAsKey('identifier')
                ->info('Workflow configuration')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                            ->end()
                            ->scalarNode('initial_stage')
                                ->defaultNull()
                            ->end()
                            ->arrayNode('stages')
                                ->beforeNormalization()
                                    ->always()
                                    ->then(static function ($stages) {
                                        foreach ($stages as $key => &$stage) {
                                            if (!isset($stage['label'])) {
                                                $stage['label'] = ucfirst($key);
                                            }
                                        }

                                        return $stages;
                                    })
                                ->end()
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('identifier')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('color')
                                            ->defaultValue(null)
                                        ->end()
                                        ->arrayNode('actions')
                                            ->prototype('array')
                                                ->children()
                                                    ->variableNode('data')->end()
                                                    ->arrayNode('condition')
                                                        ->beforeNormalization()->castToArray()->end()
                                                        ->prototype('scalar')->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->scalarNode('label')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->booleanNode('last_stage')
                                            ->info(
                                                'Flag indicating that workflow finished on this stage.'
                                                . ' When `last_stage` is `true`, content items at this stage won\'t'
                                                . ' be displayed on the Dashboard / Review Queue tab.'
                                            )
                                            ->defaultFalse()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('matchers')
                                ->useAttributeAsKey('identifier')
                                ->prototype('array')
                                    ->beforeNormalization()
                                        ->ifString()
                                        ->then(static function ($v) { return [$v]; })
                                    ->end()
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                            ->arrayNode('transitions')
                                ->beforeNormalization()
                                    ->always()
                                    ->then(
                                        function (array $transitions): array {
                                            foreach ($transitions as $key => &$transition) {
                                                if (!isset($transition['label'])) {
                                                    $transition['label'] = $this->humanize($key);
                                                }

                                                if (isset($transition['reverse']) && !empty($transition['reverse'])) {
                                                    $transition['from'] = $transitions[$transition['reverse']]['to'];
                                                    $transition['to'] = $transitions[$transition['reverse']]['from'];
                                                }
                                            }

                                            return $transitions;
                                        }
                                    )
                                ->end()
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('identifier')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('label')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('icon')
                                            ->defaultValue(null)
                                        ->end()
                                        ->scalarNode('color')
                                            ->defaultValue(null)
                                        ->end()
                                        ->scalarNode('reverse')
                                            ->defaultValue(null)
                                        ->end()
                                        ->arrayNode('from')
                                            ->beforeNormalization()
                                                ->ifString()
                                                ->then(static function ($v) { return [$v]; })
                                            ->end()
                                            ->requiresAtLeastOneElement()
                                            ->prototype('scalar')
                                                ->cannotBeEmpty()
                                            ->end()
                                        ->end()
                                        ->arrayNode('to')
                                            ->beforeNormalization()
                                                ->ifString()
                                                ->then(static function ($v) { return [$v]; })
                                            ->end()
                                            ->requiresAtLeastOneElement()
                                            ->prototype('scalar')
                                                ->cannotBeEmpty()
                                            ->end()
                                        ->end()
                                        ->arrayNode('actions')
                                            ->prototype('array')
                                                ->children()
                                                    ->variableNode('data')->end()
                                                    ->arrayNode('condition')
                                                        ->beforeNormalization()->castToArray()->end()
                                                        ->prototype('scalar')->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('reviewers')
                                            ->canBeDisabled()
                                            ->children()
                                                ->booleanNode('required')
                                                    ->defaultFalse()
                                                ->end()
                                                ->integerNode('user_group')
                                                    ->min(0)
                                                    ->defaultValue(null)
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('notification')
                                            ->children()
                                                ->arrayNode('user_group')
                                                    ->info('User group Id (or array of Ids) to receive notification')
                                                    ->beforeNormalization()->castToArray()->end()
                                                    ->prototype('scalar')->end()
                                                ->end()
                                                ->arrayNode('user')
                                                    ->info('User Id (or array of Ids) to receive notification')
                                                    ->beforeNormalization()->castToArray()->end()
                                                    ->prototype('scalar')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->booleanNode('validate')
                                            ->info('Enable/disable validate form before send')
                                            ->defaultFalse()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Does semantic config to internal container parameters mapping for $currentScope.
     *
     * This method is called by the `ConfigurationProcessor`, for each available scopes (e.g. SiteAccess, SiteAccess groups or "global").
     *
     * @param array $scopeSettings Parsed semantic configuration for current scope.
     *                             It is passed by reference, making it possible to alter it for usage after `mapConfig()` has run.
     * @param string $currentScope
     * @param \Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface $contextualizer
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (!empty($scopeSettings['workflows'])) {
            $settings = $scopeSettings['workflows'];

            $contextualizer->setContextualParameter(
                'workflows',
                $currentScope,
                $settings
            );
        }

        if (!empty($scopeSettings['workflows_config'])) {
            $settings = $scopeSettings['workflows_config'];

            if (!empty($settings['pagination']['workflow_limit'])) {
                $contextualizer->setContextualParameter(
                    'workflows_config.pagination.workflow_limit',
                    $currentScope,
                    $settings['pagination']['workflow_limit']
                );
            }

            if (!empty($settings['pagination']['suggested_reviewers_limit'])) {
                $contextualizer->setContextualParameter(
                    'workflows_config.pagination.suggested_reviewers_limit',
                    $currentScope,
                    $settings['pagination']['suggested_reviewers_limit']
                );
            }

            if (!empty($settings['matcher_value_templates'])) {
                $contextualizer->setContextualParameter(
                    'workflows_config.matcher_value_templates',
                    $currentScope,
                    $settings['matcher_value_templates']
                );
            }

            if (!empty($settings['timeline_entry_templates'])) {
                $contextualizer->setContextualParameter(
                    'workflows_config.timeline_entry_templates',
                    $currentScope,
                    $settings['timeline_entry_templates']
                );
            }
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function humanize(string $value): string
    {
        return ucfirst(strtolower(trim(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $value))));
    }
}

class_alias(WorkflowParser::class, 'EzSystems\EzPlatformWorkflowBundle\DependencyInjection\Configuration\Parser\WorkflowParser');
