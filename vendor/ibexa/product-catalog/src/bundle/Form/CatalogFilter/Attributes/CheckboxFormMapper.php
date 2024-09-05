<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\CatalogFilter\Attributes;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CheckboxAttributeCriterionTransformer;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductAttribute;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CheckboxFormMapper implements FilterFormMapperInterface, TranslationContainerInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

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

        $options = [
            'label' => $attributeDefinition->getName(),
            'block_prefix' => 'catalog_criteria_product_attribute_checkbox',
            'translation_domain' => 'ibexa_product_catalog',
            'choices' => [
                true => true,
                false => false,
            ],
            'expanded' => true,
            'required' => false,
            'choice_label' => function (?bool $choice, string $key, ?string $value): ?string {
                if ($value !== null) {
                    return $this->translator->trans(
                        /** @Ignore */
                        'filter.product_attribute.checkbox.value.' . $value,
                        [],
                        'ibexa_product_catalog'
                    );
                }

                return null;
            },
        ];

        $form = $builder->create($filterDefinition->getIdentifier(), ChoiceType::class, $options);
        $form->addModelTransformer(
            new CheckboxAttributeCriterionTransformer($attributeDefinition->getIdentifier())
        );

        $builder->add($form);
    }

    public function supports(FilterDefinitionInterface $filterDefinition): bool
    {
        if ($filterDefinition instanceof ProductAttribute) {
            return $filterDefinition->getAttributeDefinition()->getType()->getIdentifier() === 'checkbox';
        }

        return false;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create('filter.product_attribute.checkbox.value.0', 'ibexa_product_catalog')->setDesc('No'),
            Message::create('filter.product_attribute.checkbox.value.1', 'ibexa_product_catalog')->setDesc('Yes'),
        ];
    }
}
