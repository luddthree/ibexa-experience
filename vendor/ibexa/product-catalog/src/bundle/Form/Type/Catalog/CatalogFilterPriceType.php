<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogFilterPriceData;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\MoneyCurrencyTransformer;
use Ibexa\Bundle\ProductCatalog\Form\Type\CurrencyChoiceType;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion\IsCurrencyEnabledCriterion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogFilterPriceType extends AbstractType
{
    private CurrencyServiceInterface $currencyService;

    public function __construct(CurrencyServiceInterface $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $priceBlockPrefix = 'catalog_criteria_product_price';

        $builder
            ->add('currency', CurrencyChoiceType::class, [
                'criterion' => new IsCurrencyEnabledCriterion(),
                'required' => false,
            ])
            ->add('min_price', NumberType::class, [
                'block_prefix' => $priceBlockPrefix,
                'html5' => true,
            ])
            ->add('max_price', NumberType::class, [
                'block_prefix' => $priceBlockPrefix,
                'html5' => true,
            ]);

        $builder->get('currency')
            ->addModelTransformer(new MoneyCurrencyTransformer($this->currencyService));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CatalogFilterPriceData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }
}
