<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ImageEditor\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class ImageEditor extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('image_editor')
                ->children()
                    ->floatNode('image_quality')
                        ->info('Image Editor output image quality')
                        ->min(0.00)
                        ->max(1.00)
                    ->end()
                    ->arrayNode('action_groups')
                        ->info('Image Editor Actions configuration')
                        ->useAttributeAsKey('id')
                        ->arrayPrototype()
                        ->children()
                            ->scalarNode('id')->end()
                            ->scalarNode('label')->end()
                            ->arrayNode('actions')
                                ->useAttributeAsKey('id')
                                ->arrayPrototype()
                                ->children()
                                    ->scalarNode('id')->end()
                                    ->scalarNode('label')->end()
                                    ->integerNode('priority')->defaultValue(0)->end()
                                    ->booleanNode('visible')->defaultTrue()->end()
                                    ->arrayNode('buttons')
                                        ->useAttributeAsKey('id')
                                        ->arrayPrototype()
                                        ->ignoreExtraKeys(false)
                                        ->children()
                                            ->scalarNode('id')->end()
                                            ->scalarNode('label')->end()
                                            ->integerNode('priority')->defaultValue(0)->end()
                                            ->booleanNode('visible')->defaultTrue()->end()
                                        ->end()
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
        if (empty($scopeSettings['image_editor'])) {
            return;
        }

        $settings = $scopeSettings['image_editor'];

        if (!empty($settings['action_groups'])) {
            $scopeSettings['image_editor.action_groups'] = $settings['action_groups'];
        }

        if (!empty($settings['image_quality'])) {
            $contextualizer->setContextualParameter(
                'image_editor.image_quality',
                $currentScope,
                $settings['image_quality']
            );
        }
    }

    public function postMap(array $config, ContextualizerInterface $contextualizer)
    {
        $contextualizer->mapConfigArray(
            'image_editor.action_groups',
            $config
        );
    }
}

class_alias(ImageEditor::class, 'Ibexa\Platform\Bundle\ImageEditor\DependencyInjection\Configuration\Parser\ImageEditor');
