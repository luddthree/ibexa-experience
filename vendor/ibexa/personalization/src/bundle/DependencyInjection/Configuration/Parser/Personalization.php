<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

final class Personalization extends AbstractParser
{
    public const IMAGE_ATTR_NAME = 'image';
    public const DESCRIPTION_ATTR_NAME = 'description';
    public const TITLE_ATTR_NAME = 'title';

    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('personalization')
                ->info('Personalization configuration')
                ->children()
                    ->scalarNode('site_name')
                        ->info('Site name for internal usage e.g. Displaying name of Personalization limitations')
                        ->example('My site name')
                    ->end()
                    ->arrayNode('authentication')
                        ->children()
                            ->scalarNode('customer_id')
                                ->info('Personalization customer ID')
                                ->example('12345')
                                ->isRequired()
                            ->end()
                            ->scalarNode('license_key')
                                ->info('Personalization license key')
                                ->example('1234-5678-9012-3456-7890')
                                ->isRequired()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('included_item_types')
                        ->info('Content types on which tracking code will be shown')
                        ->example(['article', 'blog_post'])
                        ->scalarPrototype()->end()
                    ->end()
                    ->scalarNode('host_uri')
                        ->info('HTTP base URI of the IBEXA DXP server')
                        ->example('http://site.com')
                    ->end()
                    ->scalarNode('author_id')
                        ->info('Default content author')
                        ->example('14')
                    ->end()
                    ->arrayNode('export')
                        ->children()
                            ->arrayNode('authentication')
                                ->setDeprecated(
                                    'ibexa/personalization',
                                    '4.5.0',
                                    'Authentication export setting is deprecated and no longer used. It can be safely removed.'
                                )
                                ->children()
                                    ->scalarNode('method')
                                        ->info('Export authentication method')
                                        ->example('basic / user / none')
                                    ->end()
                                    ->scalarNode('login')
                                        ->info('Login for export authentication method')
                                    ->end()
                                    ->scalarNode('password')
                                        ->info('Password for export authentication method')
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('document_root')
                                ->defaultValue('%kernel.project_dir%/public/var/export/')
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('user_api')
                        ->children()
                            ->scalarNode('default_source')
                                ->info('User API default source name')
                                ->example('source_name-en')
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('pagination')
                    ->info('Pagination configuration')
                        ->children()
                            ->integerNode('limit')->isRequired()->end()
                        ->end()
                    ->end()
                    ->arrayNode('recommendations')
                        ->info('Recommendations preview items configuration')
                        ->children()
                            ->integerNode('limit')->end()
                            ->integerNode('max_value')->max(51)->end()
                            ->scalarNode('user_id')->end()
                        ->end()
                    ->end()
                    ->arrayNode('output_type_attributes')
                        ->beforeNormalization()
                            ->ifTrue(
                                static function (array $outputTypes) {
                                    foreach (array_keys($outputTypes) as $key) {
                                        if (!is_int($key)) {
                                            return $key;
                                        }
                                    }

                                    return false;
                                }
                            )
                            ->thenInvalid('Invalid output type in %s. Output type id should be type of int.')
                        ->end()
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode(self::TITLE_ATTR_NAME)->end()
                                ->scalarNode(self::DESCRIPTION_ATTR_NAME)->end()
                                ->scalarNode(self::IMAGE_ATTR_NAME)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('repository')
                        ->children()
                            ->arrayNode('content')
                                ->children()
                                    ->booleanNode('use_remote_id')
                                        ->info('Use remote id instead of numeric content id to process recommendations')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['personalization'])) {
            return;
        }

        $settings = $scopeSettings['personalization'];

        if (!empty($settings['output_type_attributes'])) {
            $contextualizer->setContextualParameter(
                'personalization.output_type_attributes',
                $currentScope,
                $settings['output_type_attributes']
            );
        }

        if (!empty($settings['pagination']['limit'])) {
            $contextualizer->setContextualParameter(
                'personalization.pagination.limit',
                $currentScope,
                $settings['pagination']['limit']
            );
        }

        if (!empty($settings['recommendations']['limit'])) {
            $contextualizer->setContextualParameter(
                'personalization.recommendations.limit',
                $currentScope,
                $settings['recommendations']['limit']
            );
        }

        if (!empty($settings['recommendations']['max_value'])) {
            $contextualizer->setContextualParameter(
                'personalization.recommendations.max_value',
                $currentScope,
                $settings['recommendations']['max_value']
            );
        }

        if (!empty($settings['recommendations']['user_id'])) {
            $contextualizer->setContextualParameter(
                'personalization.recommendations.user_id',
                $currentScope,
                $settings['recommendations']['user_id']
            );
        }

        if (!empty($settings['site_name'])) {
            $contextualizer->setContextualParameter(
                'site_name',
                $currentScope,
                $settings['site_name']
            );
        }

        if (!empty($settings['authentication']['customer_id'])) {
            $contextualizer->setContextualParameter(
                'personalization.authentication.customer_id',
                $currentScope,
                $settings['authentication']['customer_id']
            );
        }

        if (!empty($settings['authentication']['license_key'])) {
            $contextualizer->setContextualParameter(
                'personalization.authentication.license_key',
                $currentScope,
                $settings['authentication']['license_key']
            );
        }

        if (!empty($settings['included_item_types'])) {
            $contextualizer->setContextualParameter(
                'personalization.included_item_types',
                $currentScope,
                $settings['included_item_types']
            );
        }

        if (!empty($settings['host_uri'])) {
            $contextualizer->setContextualParameter(
                'personalization.host_uri',
                $currentScope,
                $settings['host_uri']
            );
        }

        if (!empty($settings['author_id'])) {
            $contextualizer->setContextualParameter(
                'personalization.author_id',
                $currentScope,
                $settings['author_id']
            );
        }

        if (!empty($settings['export']['authentication']['method'])) {
            $contextualizer->setContextualParameter(
                'personalization.export.authentication.method',
                $currentScope,
                $settings['export']['authentication']['method']
            );
        }

        if (!empty($settings['export']['authentication']['login'])) {
            $contextualizer->setContextualParameter(
                'personalization.export.authentication.login',
                $currentScope,
                $settings['export']['authentication']['login']
            );
        }

        if (!empty($settings['export']['authentication']['password'])) {
            $contextualizer->setContextualParameter(
                'personalization.export.authentication.password',
                $currentScope,
                $settings['export']['authentication']['password']
            );
        }

        if (!empty($settings['user_api']['default_source'])) {
            $contextualizer->setContextualParameter(
                'personalization.user_api.default_source',
                $currentScope,
                $settings['user_api']['default_source']
            );
        }

        if (!empty($settings['repository']['content']['use_remote_id'])) {
            $contextualizer->setContextualParameter(
                'personalization.repository.content.use_remote_id',
                $currentScope,
                $settings['repository']['content']['use_remote_id']
            );
        }
    }
}

class_alias(Personalization::class, 'Ibexa\Platform\Bundle\Personalization\DependencyInjection\Configuration\Parser\Personalization');
