<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\CatalogFilter;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductCategoryCriterionTransformer;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductCategoryTransformer;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\StringToArrayTransformer;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductCategory;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductCategoryFormMapper implements FilterFormMapperInterface
{
    private TaxonomyServiceInterface $taxonomyService;

    private string $productTaxonomyName;

    public function __construct(
        TaxonomyServiceInterface $taxonomyService,
        string $productTaxonomyName
    ) {
        $this->taxonomyService = $taxonomyService;
        $this->productTaxonomyName = $productTaxonomyName;
    }

    public function createFilterForm(
        FilterDefinitionInterface $filterDefinition,
        FormBuilderInterface $builder,
        array $context = []
    ): void {
        $builder->add(
            $filterDefinition->getIdentifier(),
            TextType::class,
            [
                'label' => 'filter.product_category.label',
                'block_prefix' => 'catalog_criteria_product_category',
                'translation_domain' => 'ibexa_product_catalog',
                'attr' => [
                    'data-product-taxonomy-count' => $this->taxonomyService->countAllEntries(
                        $this->productTaxonomyName
                    ),
                    'data-product-taxonomy-name' => $this->productTaxonomyName,
                ],
            ]
        );

        $builder
            ->get($filterDefinition->getIdentifier())
            ->addModelTransformer(
                new ProductCategoryCriterionTransformer($this->taxonomyService)
            )
            ->addViewTransformer(
                new StringToArrayTransformer(new ProductCategoryTransformer($this->taxonomyService))
            );
    }

    public function supports(FilterDefinitionInterface $filterDefinition): bool
    {
        return $filterDefinition instanceof ProductCategory;
    }
}
