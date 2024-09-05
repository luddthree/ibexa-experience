<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\VatCategoryChoiceTransformer;
use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;
use Ibexa\Contracts\ProductCatalog\VatServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class VatCategoryChoiceType extends AbstractType
{
    private VatServiceInterface $vatService;

    public function __construct(
        VatServiceInterface $vatService
    ) {
        $this->vatService = $vatService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['data_class'] === null) {
            // Add model transformer for identifier-based selection.
            $region = $options['region'];
            $builder->addModelTransformer(
                new VatCategoryChoiceTransformer(
                    $this->vatService,
                    $region
                )
            );
        }
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('region');
        $resolver->setAllowedTypes('region', RegionInterface::class);

        $resolver->setDefaults([
            'choice_label' => static fn (VatCategoryInterface $vatCategory): string => sprintf(
                '%s (%s)',
                $vatCategory->getIdentifier(),
                $vatCategory->getVatValue() !== null ? number_format($vatCategory->getVatValue(), 2) . '%' : 'N/A'
            ),
            'choice_value' => 'identifier',
            'choice_loader' => function (Options $options): ChoiceLoaderInterface {
                /** @var \Ibexa\Contracts\ProductCatalog\Values\RegionInterface $region */
                $region = $options['region'];

                return ChoiceList::lazy(
                    $this,
                    fn (): iterable => $this->vatService->getVatCategories($region),
                    [
                        'region_identifier' => $region->getIdentifier(),
                    ],
                );
            },
            'label' => false,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }
}
