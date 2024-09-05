<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\CatalogFilter;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductCodeCriterionTransformer;
use Ibexa\Bundle\ProductCatalog\Form\Type\TagifyType;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductCode;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductCodeFilterFormMapper implements FilterFormMapperInterface
{
    public function createFilterForm(
        FilterDefinitionInterface $filterDefinition,
        FormBuilderInterface $builder,
        array $context = []
    ): void {
        $builder->add(
            $filterDefinition->getIdentifier(),
            TagifyType::class,
            [
                'label' => 'filter.product_code.label',
                'block_prefix' => 'catalog_criteria_product_code',
                'translation_domain' => 'ibexa_product_catalog',
            ]
        );

        $builder->get($filterDefinition->getIdentifier())
            ->addModelTransformer(
                new ProductCodeCriterionTransformer()
            );
    }

    public function supports(FilterDefinitionInterface $filterDefinition): bool
    {
        return $filterDefinition instanceof ProductCode;
    }
}
