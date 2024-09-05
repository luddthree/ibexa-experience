<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\CatalogFilter\Attributes;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ColorAttributeCriterionTransformer;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\StringToArrayTransformer;
use Ibexa\Bundle\ProductCatalog\Form\Type\TagifyType;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductAttribute;
use Symfony\Component\Form\FormBuilderInterface;

final class ColorFormMapper implements FilterFormMapperInterface
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
            TagifyType::class,
            [
                'label' => $attributeDefinition->getName(),
                'block_prefix' => 'catalog_criteria_product_attribute_color',
                'translation_domain' => 'ibexa_product_catalog',
            ]
        );

        $form->addModelTransformer(new ColorAttributeCriterionTransformer($attributeDefinition->getIdentifier()));
        $form->addViewTransformer(new StringToArrayTransformer());

        $builder->add($form);
    }

    public function supports(FilterDefinitionInterface $filterDefinition): bool
    {
        if ($filterDefinition instanceof ProductAttribute) {
            return $filterDefinition->getAttributeDefinition()->getType()->getIdentifier() === 'color';
        }

        return false;
    }
}
