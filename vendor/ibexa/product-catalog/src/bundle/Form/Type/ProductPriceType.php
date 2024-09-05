<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\ProductPriceDataInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductPriceType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['currency'] = $options['currency'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currency = $options['currency'];
        assert($currency instanceof CurrencyInterface);

        $builder
            ->add('base_price', ProductPriceBasePriceType::class, [
                'currency' => $currency->getCode(),
                'scale' => $currency->getSubUnits(),
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'product_price';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductPriceDataInterface::class,
        ]);

        $resolver->setRequired('currency');
        $resolver->setAllowedTypes('currency', [
            CurrencyInterface::class,
        ]);
    }
}
