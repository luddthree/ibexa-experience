<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\DependencyInjection\Configuration\RepositoryAware;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParserInterface;
use Ibexa\ProductCatalog\Config\ConfigProvider;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class ProductCatalogParser implements RepositoryConfigParserInterface
{
    private const KEY_EXTRAS = 'extras';
    private const KEY_VALUE = 'value';
    private const KEY_NOT_APPLICABLE = 'not_applicable';

    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode(ConfigProvider::PRODUCT_CATALOG_ROOT)
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('engine')
                        ->info('The product catalog engine to use')
                        ->defaultNull()
                    ->end()
                    ->append($this->getRegionConfigurationTree())
                ->end()
            ->end();
    }

    private function getRegionConfigurationTree(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('regions');
        $node = $treeBuilder->getRootNode();
        $node->fixXmlConfig('region');
        $node
            ->normalizeKeys(false)
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->normalizeKeys(false)
                ->fixXmlConfig('vat_category', 'vat_categories')
                ->children()
                    ->arrayNode('vat_categories')
                        ->useAttributeAsKey('name')
                        ->beforeNormalization()
                            ->always(
                                static function (array $value): array {
                                    foreach ($value as $vatCategory => $vatConfig) {
                                        if (null === $vatConfig) {
                                            $value[$vatCategory] = [self::KEY_VALUE => null];
                                        }

                                        if (is_scalar($vatConfig)) {
                                            $value[$vatCategory] = [self::KEY_VALUE => $vatConfig];
                                        }

                                        $hasNullVatValue = ($value[$vatCategory][self::KEY_VALUE] ?? null) === null;
                                        if ($hasNullVatValue) {
                                            $value[$vatCategory][self::KEY_VALUE] = 0;
                                        }

                                        $hasNotApplicableFlag = isset($vatConfig[self::KEY_EXTRAS][self::KEY_NOT_APPLICABLE]);
                                        if ($hasNullVatValue && !$hasNotApplicableFlag) {
                                            $value[$vatCategory][self::KEY_EXTRAS][self::KEY_NOT_APPLICABLE] = true;
                                        }
                                    }

                                    return $value;
                                }
                            )
                        ->end()
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode(self::KEY_VALUE)
                                    ->isRequired()
                                    ->validate()
                                        ->ifTrue(
                                            static function ($value): bool {
                                                return !is_numeric($value) || $value > 100 || $value < 0;
                                            }
                                        )
                                        ->thenInvalid('VAT setting should be a numeric value between 0 and 100, or NULL')
                                    ->end()
                                ->end()
                                ->arrayNode(self::KEY_EXTRAS)
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
