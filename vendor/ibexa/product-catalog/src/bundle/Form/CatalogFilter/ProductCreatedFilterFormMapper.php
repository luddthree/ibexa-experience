<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\CatalogFilter;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CreatedAtCriterionTransformer;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogFilterCreatedType;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductCreated;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductCreatedFilterFormMapper implements FilterFormMapperInterface
{
    public function createFilterForm(
        FilterDefinitionInterface $filterDefinition,
        FormBuilderInterface $builder,
        array $context = []
    ): void {
        $builder->add(
            $filterDefinition->getIdentifier(),
            CatalogFilterCreatedType::class,
            [
                'label' => 'filter.product_created.label',
                'block_prefix' => 'catalog_criteria_product_created',
                'translation_domain' => 'ibexa_product_catalog',
            ]
        );

        $builder
            ->get($filterDefinition->getIdentifier())
            ->addModelTransformer(
                new CreatedAtCriterionTransformer()
            );
    }

    public function supports(FilterDefinitionInterface $filterDefinition): bool
    {
        return $filterDefinition instanceof ProductCreated;
    }
}
