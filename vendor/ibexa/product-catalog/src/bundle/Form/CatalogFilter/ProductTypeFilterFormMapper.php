<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\CatalogFilter;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductTypeCriterionTransformer;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeChoiceType;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductTypeFilterFormMapper implements FilterFormMapperInterface
{
    private ProductTypeServiceInterface $productTypeService;

    public function __construct(
        ProductTypeServiceInterface $productTypeService
    ) {
        $this->productTypeService = $productTypeService;
    }

    public function createFilterForm(
        FilterDefinitionInterface $filterDefinition,
        FormBuilderInterface $builder,
        array $context = []
    ): void {
        $builder->add(
            $filterDefinition->getIdentifier(),
            ProductTypeChoiceType::class,
            [
                'expanded' => true,
                'multiple' => true,
                'label' => 'filter.product_type.label',
                'block_prefix' => 'catalog_criteria_product_type',
                'translation_domain' => 'ibexa_product_catalog',
            ]
        );
        $builder->get($filterDefinition->getIdentifier())
            ->addModelTransformer(
                new ProductTypeCriterionTransformer(
                    $this->productTypeService
                )
            );
    }

    public function supports(FilterDefinitionInterface $filterDefinition): bool
    {
        return $filterDefinition instanceof ProductType;
    }
}
