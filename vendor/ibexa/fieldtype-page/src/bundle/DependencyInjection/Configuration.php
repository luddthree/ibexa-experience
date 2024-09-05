<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\FieldTypePage\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @phpstan-type TAttribute array{
 *     name?: string,
 *     type: string,
 *     validators?: array<string, array{
 *         identifier?: string,
 *         message?: string,
 *         options?: mixed,
 *     }>,
 *     identifier?: string,
 *     value?: mixed,
 *     category: string,
 *     options?: array<mixed>,
 * }
 *
 * @phpstan-type TAttributeNormalized array{
 *     identifier: string,
 *     name?: string,
 *     type: string,
 *     validators?: array<string, array{
 *         identifier?: string,
 *         message?: string,
 *         options?: mixed,
 *     }>,
 *     value?: mixed,
 *     category: string,
 *     options?: array<mixed>,
 * }
 *
 * @phpstan-type TView array{name:string, template: string, priority: integer}
 *
 * @phpstan-type TBlock array{
 *     name?: string,
 *     initialize?: bool,
 *     ttl?: integer,
 *     category?: string,
 *     thumbnail?:string,
 *     visible?: bool,
 *     configuration_template?: string,
 *     views?: array<string, TView>,
 *     attributes?: array<string, TAttribute>,
 * }
 *
 * @phpstan-type TBlockNormalized array{
 *     identifier: string,
 *     name?: string,
 *     initialize?: bool,
 *     ttl?: integer,
 *     category?: string,
 *     thumbnail?:string,
 *     visible?: bool,
 *     configuration_template?: string,
 *     views?: array<string, TView>,
 *     attributes?: array<string, TAttribute>,
 * }
 *
 * @phpstan-type TBlockNormalizedWithNormalizedAttributes array{
 *     identifier: string,
 *     name?: string,
 *     initialize?: bool,
 *     ttl?: integer,
 *     category?: string,
 *     thumbnail?:string,
 *     visible?: bool,
 *     configuration_template?: string,
 *     views?: array<string, TView>,
 *     attributes: array<string, TAttribute>,
 * }
 *
 * @phpstan-type TReactBlock array{
 *     name?: string,
 *     initialize?: bool,
 *     ttl?: integer,
 *     category?: string,
 *     thumbnail?:string,
 *     visible?: bool,
 *     configuration_template?: string,
 *     component?: string,
 *     attributes?: array<string, TAttribute>,
 * }
 *
 * @phpstan-type TReactBlockNormalized array{
 *     identifier: string,
 *     name?: string,
 *     initialize?: bool,
 *     ttl?: integer,
 *     category?: string,
 *     thumbnail?:string,
 *     visible?: bool,
 *     configuration_template?: string,
 *     component?: string,
 *     attributes?: array<string, TAttribute>,
 * }
 *
 * @phpstan-type TReactBlockNormalizedWithNormalizedAttributes array{
 *      identifier: string,
 *      name?: string,
 *      initialize?: bool,
 *      ttl?: integer,
 *      category?: string,
 *      thumbnail?:string,
 *      visible?: bool,
 *      configuration_template?: string,
 *      component?: string,
 *      attributes: array<string, TAttribute>,
 *  }
 *
 * @phpstan-type TLayout array{
 *     identifier: string,
 *     name: string,
 *     description: string,
 *     thumbnail: string,
 *     template: string,
 *     zones: array<string, array<mixed>>,
 *     visible: bool,
 * }
 *
 * @phpstan-type TBlockConfiguration array{
 *     identifier: string,
 *     name: string|null,
 *     category: string|null,
 *     thumbnail: string|null,
 *     visible: bool,
 *     configuration_template: string,
 *     views: array<string, TView>,
 *     attributes: array<string, TAttribute>,
 * }
 * @phpstan-type TReactBlockConfiguration array{
 *     identifier: string,
 *     name: string|null,
 *     category: string|null,
 *     thumbnail: string|null,
 *     visible: bool,
 *     configuration_template: string,
 *     component: string,
 *     attributes: array<string, TAttribute>,
 * }
 */
class Configuration implements ConfigurationInterface
{
    public const BC_NOTICE = 'not used, provided to preserve backward compatibility';
    public const DEFAULT_CATEGORY = 'default';
    public const KEY_REACT_BLOCKS = 'react_blocks';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(IbexaFieldTypePageExtension::EXTENSION_NAME);

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->beforeNormalization()
                ->ifTrue(
                    /**
                     * @phpstan-param array<string, array<string, TBlock>> $value
                     */
                    static function (array $value): bool {
                        return !empty($value['blocks']);
                    }
                )
                ->then(
                    /**
                     * @phpstan-param array<string, array<string, TBlock>> $value
                     * @phpstan-return array<string, array<string, TBlockNormalized>>
                     */
                    static function (array $value): array {
                        // maps array key to `identifier` element
                        foreach ($value['blocks'] as $identifier => $block) {
                            $value['blocks'][$identifier]['identifier'] = $identifier;
                        }

                        return $value;
                    }
                )
            ->end()
            ->beforeNormalization()
                ->ifTrue(
                    /**
                     * @phpstan-param array<string, array<string, TReactBlock>> $value
                     */
                    static function (array $value): bool {
                        return !empty($value['react_blocks']);
                    }
                )
                ->then(
                    /**
                     * @phpstan-param array<string, array<string, TReactBlock>> $value
                     * @phpstan-return array<string, array<string, TReactBlockNormalized>>
                     */
                    static function (array $value): array {
                        // maps array key to `identifier` element
                        foreach ($value['react_blocks'] as $identifier => $block) {
                            $value['react_blocks'][$identifier]['identifier'] = $identifier;
                        }

                        return $value;
                    }
                )
            ->end()
            ->children()
                ->arrayNode('layouts')
                    ->useAttributeAsKey('identifier', false)
                    ->prototype('array')
                        ->children()
                            ->scalarNode('identifier')->end()
                            ->scalarNode('name')->end()
                            ->scalarNode('description')->end()
                            ->scalarNode('thumbnail')->end()
                            ->scalarNode('template')->end()
                            ->booleanNode('visible')
                                ->info('Is layout visible in PB/FieldType configuration?')
                                ->defaultTrue()
                            ->end()
                            ->arrayNode('zones')
                                ->useAttributeAsKey('identifier')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('name')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('block_validators')
                    ->info(
                        'Defines map of validator identifiers to their implementations (FQCN), so they can be ' .
                        'used in block definition'
                    )
                    ->example([
                        'my_validator' => 'SomeValidator\Constraint\MyCustomValidator',
                        'other_validator' => 'SomeValidator\Constraint\OtherCustomValidator',
                    ])
                    ->useAttributeAsKey('identifier', false)
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode(self::KEY_REACT_BLOCKS)
                    ->info('Configuration of additional Block Types used by Landing Pages in Ibexa DXP')
                    ->useAttributeAsKey('identifier', false)
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifTrue(
                                /**
                                 * @phpstan-param array{attributes: array<string, TAttribute>} $value
                                 */
                                static function (array $value): bool {
                                    return !empty($value['attributes']);
                                }
                            )
                            ->then(
                                /**
                                 * @phpstan-param  array{attributes: array<string, TAttribute>} $value
                                 */
                                static function (array $value): array {
                                    foreach ($value['attributes'] as $identifier => $attribute) {
                                        if (is_scalar($value['attributes'][$identifier])) {
                                            $value['attributes'][$identifier] = ['type' => $attribute];
                                        }

                                        if (empty($value['attributes'][$identifier]['identifier'])) {
                                            $value['attributes'][$identifier]['identifier'] = $identifier;
                                        }
                                    }

                                    return $value;
                                }
                            )
                        ->end()
                        ->children()
                            ->booleanNode('initialize')
                                ->defaultFalse()
                            ->end()
                            ->integerNode('ttl')
                                ->defaultValue(0)
                            ->end()
                            ->scalarNode('identifier')->end()
                            ->scalarNode('name')->end()
                            ->scalarNode('category')->end()
                            ->scalarNode('thumbnail')->end()
                            ->booleanNode('visible')
                                ->defaultTrue()
                            ->end()
                            ->variableNode('component')->end()
                            ->append($this->addAttributes())
                            ->scalarNode('configuration_template')
                                ->info('Template to be used to display Block configuration')
                                ->defaultValue('@IbexaPageBuilder/page_builder/block/config.html.twig')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('blocks')
                    ->info('Configuration of additional Block Types used by Landing Pages in Ibexa DXP')
                    ->useAttributeAsKey('identifier', false)
                    ->prototype('array')
                    ->beforeNormalization()
                        ->ifTrue(
                            /**
                             * @phpstan-param TBlockNormalized $value
                             */
                            static function (array $value): bool {
                                return !empty($value['attributes']);
                            }
                        )
                        ->then(
                            /**
                             * @phpstan-param TBlockNormalized $value
                             * @phpstan-return TBlockNormalizedWithNormalizedAttributes
                             */
                            static function (array $value): array {
                                // maps array key to `identifier` element, needed for BC
                                foreach ($value['attributes'] as $identifier => $attribute) {
                                    $value['attributes'][$identifier]['identifier'] = $value['attributes'][$identifier]['identifier'] ?? $identifier;
                                }

                                return $value;
                            }
                        )
                    ->end()
                    ->children()
                        ->booleanNode('initialize')
                            ->defaultFalse()
                        ->end()
                        ->integerNode('ttl')
                            ->defaultValue(0)
                        ->end()
                        ->scalarNode('identifier')->end()
                        ->scalarNode('name')->end()
                        ->scalarNode('category')->end()
                        ->scalarNode('thumbnail')->end()
                        ->booleanNode('visible')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('configuration_template')
                            ->info('Template to be used to display Block configuration')
                            ->defaultValue('@IbexaPageBuilder/page_builder/block/config.html.twig')
                        ->end()
                        ->append($this->addViews())
                        ->append($this->addAttributes())
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    public function addAttributes(): NodeDefinition
    {
        $builder = new TreeBuilder('attributes');
        $node = $builder->getRootNode();

        $node
            ->info('Attributes allow to configure your block')
            ->useAttributeAsKey('identifier', false)
            ->beforeNormalization()
            ->ifTrue(static function (array $value): bool {
                return !empty($value['validators']);
            })
            ->then(static function (array $value): array {
                // maps array key to `identifier` element, needed for BC
                foreach ($value['validators'] as $identifier => $attribute) {
                    $value['validators'][$identifier]['identifier'] = $value['validators'][$identifier]['identifier'] ?? $identifier;
                }

                return $value;
            })
            ->end()
            ->prototype('array')
                ->beforeNormalization()
                    ->ifString()
                    ->then(static function ($v): array {
                        return ['type' => $v];
                    })
                ->end()
                ->children()
                    ->arrayNode('validators')
                        ->info('Constraints of the attribute value')
                        ->useAttributeAsKey('identifier', false)
                        ->prototype('array')
                            ->children()
                                ->scalarNode('identifier')->end()
                                ->scalarNode('message')->end()
                                ->variableNode('options')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->variableNode('identifier')->end()
                    ->variableNode('options')->end()
                    ->scalarNode('category')
                        ->defaultValue(self::DEFAULT_CATEGORY)
                    ->end()
                    ->variableNode('value')->end()
                    ->scalarNode('name')->end()
                    ->scalarNode('type')->end()
                    ->variableNode('options')
                        ->info('Options are used by types select, multiple, radio, nested_attribute')
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function addViews(): NodeDefinition
    {
        $builder = new TreeBuilder('views');
        $node = $builder->getRootNode();

        $node
            ->info(
                'You can define array of views as in the example below but in case of only one view' .
                'you can pass string containing template path'
            )
            ->example([
                'first_view' => [
                    'name' => 'Basic block template',
                    'template' => 'AppBundle::first_view.html.twig',
                ],
                'second_view' => 'AppBundle::second_view.html.twig',
            ])
            ->useAttributeAsKey('identifier', false)
            ->beforeNormalization()
                ->ifString()
                ->then(static function (array $v): array {
                    return ['default' => $v];
                })
            ->end()
            ->prototype('array')
                ->beforeNormalization()
                    ->ifString()
                    ->then(static function (array $v): array {
                        return ['template' => $v];
                    })
                ->end()
                ->children()
                    ->scalarNode('name')
                        ->defaultValue('Default view')
                    ->end()
                    ->scalarNode('template')
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('options')
                        ->info(
                            'Additional configuration for block'
                        )
                        ->prototype('variable')
                        ->end()
                    ->end()
                    ->integerNode('priority')
                        ->defaultValue(0)
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}

class_alias(Configuration::class, 'EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\Configuration');
