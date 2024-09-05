<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Configuration parser for Admin UI forms settings.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      admin_group: # configuration per SiteAccess or SiteAccess group
 *          page_builder_forms:
 *              block_edit_form_templates:
 *                  - { template: 'template.html.twig', priority: 0 }
 * ```
 */
class PageBuilderForms extends AbstractParser
{
    public const FORM_TEMPLATES_PARAM = 'page_builder_forms.block_edit_form_templates';

    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('page_builder_forms')
                ->info('Admin UI forms configuration settings')
                ->children()
                    ->arrayNode('block_edit_form_templates')
                        ->info('A list of page builder Block Edit (and create) default Twig form templates')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('template')->end()
                                ->integerNode('priority')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(
        array &$scopeSettings,
        $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        if (!empty($scopeSettings['page_builder_forms']['block_edit_form_templates'])) {
            $scopeSettings['page_builder_forms.block_edit_form_templates'] = $this->processContentEditFormTemplates(
                $scopeSettings['page_builder_forms']['block_edit_form_templates']
            );
            unset($scopeSettings['page_builder_forms']['block_edit_form_templates']);
        }

        $contextualizer->setContextualParameter(
            static::FORM_TEMPLATES_PARAM,
            $currentScope,
            $scopeSettings['page_builder_forms.block_edit_form_templates'] ?? []
        );
    }

    /**
     * {@inheritdoc}
     */
    public function postMap(array $config, ContextualizerInterface $contextualizer)
    {
        $contextualizer->mapConfigArray('page_builder_forms.block_edit_form_templates', $config);
    }

    /**
     * Processes given prioritized list of templates, sorts them according to their priorities and
     * returns as a simple list of templates.
     *
     * The input list of the templates needs to be in the form of:
     * <code>
     *  [
     *      [ 'template' => '<file_path>', 'priority' => <int> ],
     *  ],
     * </code>
     *
     * @param array $formTemplates
     *
     * @return array ordered list of templates
     */
    private function processContentEditFormTemplates(array $formTemplates)
    {
        $priorities = array_column($formTemplates, 'priority');
        array_multisort($priorities, SORT_DESC, $formTemplates);

        // return as a simple list of templates.
        return array_column($formTemplates, 'template');
    }
}

class_alias(PageBuilderForms::class, 'EzSystems\EzPlatformPageBuilderBundle\DependencyInjection\Configuration\Parser\PageBuilderForms');
