<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Configuration parser for Form Builder.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          form_builder:
 *              forms_location_id: 55
 *              upload_location_id: 54
 *              pagination:
 *                  submission_limit: 10
 *              captcha:
 *                  use_ajax: true
 * ```
 */
class FormBuilder extends AbstractParser
{
    public const DEFAULT_FORMS_LOCATION_ID = 55;
    public const DEFAULT_UPLOAD_LOCATION_ID = 54;

    /**
     * {@inheritdoc}
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('form_builder')
                ->info('Form Builder configuration')
                ->children()
                    ->scalarNode('forms_location_id')
                        ->info('Root location ID for the forms')
                        ->defaultValue(self::DEFAULT_FORMS_LOCATION_ID)
                    ->end()
                    ->scalarNode('upload_location_id')
                        ->info('Default location used to store uploaded files')
                        ->defaultValue(self::DEFAULT_UPLOAD_LOCATION_ID)
                    ->end()
                    ->arrayNode('pagination')
                        ->info('Pagination configuration.')
                        ->children()
                            ->scalarNode('submission_limit')->isRequired()->end()
                        ->end()
                    ->end()
                    ->arrayNode('captcha')
                        ->info('Captcha configuration')
                        ->children()
                            ->booleanNode('use_ajax')
                            ->info('Use AJAX to fetch captcha. This could solve issues when site is behind varnish.')
                            ->defaultFalse()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['form_builder'])) {
            return;
        }

        $settings = $scopeSettings['form_builder'];

        if (!empty($settings['forms_location_id'])) {
            $contextualizer->setContextualParameter(
                'form_builder.forms_location_id',
                $currentScope,
                $settings['forms_location_id']
            );
        }

        if (!empty($settings['upload_location_id'])) {
            $contextualizer->setContextualParameter(
                'form_builder.upload_location_id',
                $currentScope,
                $settings['upload_location_id']
            );
        }

        if (!empty($settings['pagination']['submission_limit'])) {
            $contextualizer->setContextualParameter(
                'form_builder.pagination.submission_limit',
                $currentScope,
                $settings['pagination']['submission_limit']
            );
        }

        if (!empty($settings['captcha']['use_ajax'])) {
            $contextualizer->setContextualParameter(
                'form_builder.captcha.use_ajax',
                $currentScope,
                $settings['captcha']['use_ajax']
            );
        }
    }
}

class_alias(FormBuilder::class, 'EzSystems\EzPlatformFormBuilderBundle\DependencyInjection\Configuration\Parser\FormBuilder');
