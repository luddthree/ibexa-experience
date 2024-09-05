<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\CatalogFilter;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductBasePriceCriterionTransformer;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogFilterPriceType;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductBasePrice;
use Ibexa\ProductCatalog\Money\DecimalMoneyFactory;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductBasePriceFilterFormMapper implements FilterFormMapperInterface
{
    private DecimalMoneyFactory $decimalMoneyParserFactory;

    public function __construct(DecimalMoneyFactory $decimalMoneyParserFactory)
    {
        $this->decimalMoneyParserFactory = $decimalMoneyParserFactory;
    }

    public function supports(FilterDefinitionInterface $filterDefinition): bool
    {
        return $filterDefinition instanceof ProductBasePrice;
    }

    public function createFilterForm(
        FilterDefinitionInterface $filterDefinition,
        FormBuilderInterface $builder,
        array $context = []
    ): void {
        $builder->add(
            $filterDefinition->getIdentifier(),
            CatalogFilterPriceType::class,
            [
                'label' => 'filter.product_price.label',
                'required' => false,
                'block_prefix' => 'catalog_criteria_product_base_price',
                'translation_domain' => 'ibexa_product_catalog',
            ]
        );

        $builder->get($filterDefinition->getIdentifier())
            ->addModelTransformer(new ProductBasePriceCriterionTransformer($this->decimalMoneyParserFactory));
    }
}
