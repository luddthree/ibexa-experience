<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\DependencyInjection\Configuration\SiteAccessAware;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogQuery;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Ibexa\Contracts\ProductCatalog\Values\Region\RegionQuery;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Configuration parser for product catalog.
 *
 * Example configuration:
 *
 * ```yaml
 * ibexa:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          product_catalog:
 *              assets:
 *                  templates:
 *                      - { template: "@ibexadesign/product_catalog/custom_assets.html.twig" }
 *              catalogs:
 *                  default_filters:
 *                      - product_type
 *                      - product_price
 *                  filter_preview_templates:
 *                      - { template: "@ibexadesign/product_catalog/catalogs/filter_preview_blocks.html.twig" }
 *              currencies:
 *                  - EUR
 *                  - USD
 *              completeness:
 *                  tasks:
 *                      - template: "@ibexadesign/product_catalog/completeness_task.html.twig"
 *                        priority: 10
 *              pagination:
 *                  attribute_definitions_limit: 10
 *                  attribute_groups_limit: 10
 *                  customer_groups_limit: 10
 *                  customer_group_users_limit: 10
 *                  products_limit: 10
 *                  product_types_limit: 10
 *                  product_view_custom_prices_limit: 10
 *                  regions_limit: 10
 *                  catalogs_limit: 10
 *              regions:
 *                  - france
 *                  - germany
 * ```
 */
final class ProductCatalogParser extends AbstractParser
{
    public const CONFIG_CURRENCIES = 'product_catalog.currencies';

    /**
     * @param array<string,mixed> $scopeSettings
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['product_catalog'])) {
            return;
        }

        $settings = $scopeSettings['product_catalog'];

        $this->addAssetsParameters($settings, $currentScope, $contextualizer);
        $this->addCatalogsParameters($settings, $currentScope, $contextualizer);
        $this->addPaginationParameters($settings, $currentScope, $contextualizer);
        $this->addCurrencyParameters($settings, $currentScope, $contextualizer);
        $this->addCompletenessParameters($settings, $currentScope, $contextualizer);
        $this->addRegionParameters($settings, $currentScope, $contextualizer);
    }

    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $rootProductCatalogNode = $nodeBuilder->arrayNode('product_catalog');

        $rootProductCatalogNode->append($this->addAssetsConfiguration());
        $rootProductCatalogNode->append($this->addCatalogsConfiguration());
        $rootProductCatalogNode->append($this->addCurrencyConfiguration());
        $rootProductCatalogNode->append($this->addCompletenessConfiguration());
        $rootProductCatalogNode->append($this->addPaginationConfiguration());
        $rootProductCatalogNode->append($this->addRegionConfiguration());
    }

    private function addAssetsConfiguration(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('assets');

        $node = $treeBuilder->getRootNode();
        $node
            ->children()
                ->arrayNode('templates')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('template')
                                ->isRequired()
                            ->end()
                            ->scalarNode('priority')
                                ->defaultValue(0)
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function addCatalogsConfiguration(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('catalogs');

        $node = $treeBuilder->getRootNode();
        $node
            ->children()
                ->arrayNode('default_filters')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('filter_preview_templates')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('template')
                                ->isRequired()
                            ->end()
                            ->scalarNode('priority')
                                ->defaultValue(0)
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function addRegionConfiguration(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('regions');
        $node = $treeBuilder->getRootNode();

        $node
            ->fixXmlConfig('region')
            ->beforeNormalization()->castToArray()->end()
            ->scalarPrototype()->end()
        ;

        return $node;
    }

    private function addCurrencyConfiguration(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('currencies');
        $node = $treeBuilder->getRootNode();

        $node
            ->fixXmlConfig('currency', 'currencies')
            ->beforeNormalization()->castToArray()->end()
            ->scalarPrototype()->end()
        ;

        return $node;
    }

    private function addCompletenessConfiguration(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('completeness');

        $node = $treeBuilder->getRootNode();
        $node
            ->children()
                ->arrayNode('tasks')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('template')
                                ->isRequired()
                            ->end()
                            ->integerNode('priority')
                                ->defaultValue(0)
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function addPaginationConfiguration(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('pagination');
        $node = $treeBuilder->getRootNode();

        $node
            ->children()
                ->integerNode('attribute_definitions_limit')
                    ->isRequired()
                    ->defaultValue(AttributeDefinitionQuery::DEFAULT_LIMIT)
                    ->min(1)
                ->end()
                ->integerNode('attribute_groups_limit')
                    ->isRequired()
                    ->defaultValue(AttributeGroupQuery::DEFAULT_LIMIT)
                    ->min(1)
                ->end()
                ->integerNode('customer_groups_limit')
                    ->isRequired()
                    ->defaultValue(CustomerGroupQuery::DEFAULT_LIMIT)
                    ->min(1)
                ->end()
                ->integerNode('customer_group_users_limit')
                    ->isRequired()
                    ->defaultValue(25)
                    ->min(1)
                ->end()
                ->integerNode('currencies_limit')
                    ->isRequired()
                    ->defaultValue(CurrencyQuery::DEFAULT_LIMIT)
                    ->min(1)
                ->end()
                ->integerNode('products_limit')
                    ->isRequired()
                    ->defaultValue(ProductQuery::DEFAULT_LIMIT)
                    ->min(1)
                ->end()
                ->integerNode('product_types_limit')
                    ->isRequired()
                    ->defaultValue(ProductTypeQuery::DEFAULT_LIMIT)
                    ->min(1)
                ->end()
                ->integerNode('product_view_custom_prices_limit')
                    ->isRequired()
                    ->defaultValue(3)
                    ->min(1)
                ->end()
                ->integerNode('regions_limit')
                    ->isRequired()
                    ->defaultValue(RegionQuery::DEFAULT_LIMIT)
                    ->min(1)
                ->end()
                ->integerNode('catalogs_limit')
                    ->isRequired()
                    ->defaultValue(CatalogQuery::DEFAULT_LIMIT)
                    ->min(1)
                ->end()
            ->end();

        return $node;
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function addAssetsParameters(
        array $settings,
        string $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        if (isset($settings['assets']['templates'])) {
            $contextualizer->setContextualParameter(
                'product_catalog.asset_templates',
                $currentScope,
                $settings['assets']['templates'],
            );
        }
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function addCatalogsParameters(
        array $settings,
        string $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        if (isset($settings['catalogs']['default_filters'])) {
            $contextualizer->setContextualParameter(
                'product_catalog.default_filters',
                $currentScope,
                $settings['catalogs']['default_filters'],
            );
        }
        if (isset($settings['catalogs']['filter_preview_templates'])) {
            $contextualizer->setContextualParameter(
                'product_catalog.filter_preview_templates',
                $currentScope,
                $settings['catalogs']['filter_preview_templates'],
            );
        }
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function addPaginationParameters(
        array $settings,
        string $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        $names = [
            'attribute_definitions_limit',
            'attribute_groups_limit',
            'currencies_limit',
            'customer_groups_limit',
            'customer_group_users_limit',
            'products_limit',
            'product_types_limit',
            'product_view_custom_prices_limit',
            'regions_limit',
            'catalogs_limit',
        ];
        foreach ($names as $name) {
            if (isset($settings['pagination'][$name])) {
                $contextualizer->setContextualParameter(
                    'product_catalog.pagination.' . $name,
                    $currentScope,
                    $settings['pagination'][$name]
                );
            }
        }
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function addCurrencyParameters(
        array $settings,
        string $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        if (empty($settings['currencies'])) {
            return;
        }

        $currencies = array_map('strval', $settings['currencies']);

        $contextualizer->setContextualParameter(
            self::CONFIG_CURRENCIES,
            $currentScope,
            $currencies,
        );
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function addCompletenessParameters(
        array $settings,
        string $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        if (!isset($settings['completeness']['tasks'])) {
            return;
        }

        $contextualizer->setContextualParameter(
            'product_catalog.completeness_tasks',
            $currentScope,
            $settings['completeness']['tasks'],
        );
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function addRegionParameters(
        array $settings,
        string $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        if (empty($settings['regions'])) {
            return;
        }

        $contextualizer->setContextualParameter(
            'product_catalog.regions',
            $currentScope,
            $settings['regions'],
        );
    }
}
