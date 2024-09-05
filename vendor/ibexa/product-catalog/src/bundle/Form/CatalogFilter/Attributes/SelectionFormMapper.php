<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\CatalogFilter\Attributes;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\SelectionAttributeCriterionTransformer;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductAttribute;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class SelectionFormMapper implements FilterFormMapperInterface
{
    private LanguageResolver $languageResolver;

    public function __construct(LanguageResolver $languageResolver)
    {
        $this->languageResolver = $languageResolver;
    }

    /**
     * @param \Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductAttribute $filterDefinition
     */
    public function createFilterForm(
        FilterDefinitionInterface $filterDefinition,
        FormBuilderInterface $builder,
        array $context = []
    ): void {
        $attributeDefinition = $filterDefinition->getAttributeDefinition();

        $form = $builder->create(
            $filterDefinition->getIdentifier(),
            ChoiceType::class,
            [
                'label' => $attributeDefinition->getName(),
                'block_prefix' => 'catalog_criteria_product_attribute_selection',
                'translation_domain' => 'ibexa_product_catalog',
                'choices' => $this->createChoices($attributeDefinition),
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ]
        );

        $form->addModelTransformer(
            new SelectionAttributeCriterionTransformer($attributeDefinition->getIdentifier())
        );

        $builder->add($form);
    }

    /**
     * @return array<string,string>
     */
    private function createChoices(AttributeDefinitionInterface $definition): iterable
    {
        $languages = $this->languageResolver->getPrioritizedLanguages();

        /** @var array<array{ label: array<string,string>, value: string }> $options */
        $options = $definition->getOptions()->get('choices', []);

        $choices = [];
        foreach ($options as $option) {
            $label = $option['value'];
            foreach ($languages as $language) {
                if (isset($option['label'][$language])) {
                    $label = $option['label'][$language];
                    break;
                }
            }

            $choices[$label] = $option['value'];
        }

        return $choices;
    }

    public function supports(FilterDefinitionInterface $filterDefinition): bool
    {
        if ($filterDefinition instanceof ProductAttribute) {
            return $filterDefinition->getAttributeDefinition()->getType()->getIdentifier() === 'selection';
        }

        return false;
    }
}
