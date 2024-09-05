<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\CatalogFilter\Attributes;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\FloatAttributeCriterionTransformer;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogFilterFloatAttributeType;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductAttribute;
use Symfony\Component\Form\FormBuilderInterface;

final class FloatFormMapper implements FilterFormMapperInterface
{
    /**
     * @param \Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductAttribute $filterDefinition
     */
    public function createFilterForm(
        FilterDefinitionInterface $filterDefinition,
        FormBuilderInterface $builder,
        array $context = []
    ): void {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface $attributeDefinition */
        $attributeDefinition = $filterDefinition->getAttributeDefinition();

        $form = $builder->create(
            $filterDefinition->getIdentifier(),
            CatalogFilterFloatAttributeType::class,
            [
                'label' => $attributeDefinition->getName(),
                'block_prefix' => 'catalog_criteria_product_attribute_float',
                'translation_domain' => 'ibexa_product_catalog',
                'min' => $attributeDefinition->getOptions()->get('min'),
                'max' => $attributeDefinition->getOptions()->get('max'),
            ]
        );

        $form->addModelTransformer(
            new FloatAttributeCriterionTransformer($attributeDefinition->getIdentifier())
        );

        $builder->add($form);
    }

    public function supports(FilterDefinitionInterface $filterDefinition): bool
    {
        if ($filterDefinition instanceof ProductAttribute) {
            return $filterDefinition->getAttributeDefinition()->getType()->getIdentifier() === 'float';
        }

        return false;
    }
}
