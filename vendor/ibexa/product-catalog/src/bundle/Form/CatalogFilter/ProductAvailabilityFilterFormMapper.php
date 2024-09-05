<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\CatalogFilter;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductAvailabilityCriterionTransformer;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\ProductAvailability;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Annotation\Ignore;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductAvailabilityFilterFormMapper implements FilterFormMapperInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function createFilterForm(
        FilterDefinitionInterface $filterDefinition,
        FormBuilderInterface $builder,
        array $context = []
    ): void {
        $builder->add(
            $filterDefinition->getIdentifier(),
            ChoiceType::class,
            [
                'choices' => [
                    true => true,
                    false => false,
                ],
                'choice_label' => function (?bool $choice, string $key, ?string $value): ?string {
                    if ($value !== null) {
                        return $this->translator->trans(
                            /** @Ignore */
                            'filter.product_availability.value.' . $value,
                            [],
                            'ibexa_product_catalog'
                        );
                    }

                    return null;
                },
                'label' => 'filter.product_availability.label',
                'required' => false,
                'block_prefix' => 'catalog_criteria_product_availability',
                'expanded' => true,
                'translation_domain' => 'ibexa_product_catalog',
                'placeholder' => $this->translator->trans(
                    /** @Desc("All") */
                    'filter.product_availability.placeholder',
                    [],
                    'ibexa_product_catalog'
                ),
            ]
        );

        $builder->get($filterDefinition->getIdentifier())
            ->addModelTransformer(new ProductAvailabilityCriterionTransformer());
    }

    public function supports(FilterDefinitionInterface $filterDefinition): bool
    {
        return $filterDefinition instanceof ProductAvailability;
    }
}
