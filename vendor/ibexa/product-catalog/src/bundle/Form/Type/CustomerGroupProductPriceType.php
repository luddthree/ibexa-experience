<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\CustomerGroupAwareInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerGroupProductPriceType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $data = $form->getData();
        assert($data instanceof CustomerGroupAwareInterface);

        $view->vars['customer_group'] = $data->getCustomerGroup();
        $view->vars['custom_price_rule'] = $data->getCustomPriceRule();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('base_price', ProductPriceBasePriceType::class, [
                'currency' => $options['currency']->getCode(),
                'scale' => $options['currency']->getSubUnits(),
            ])
            ->add('custom_price', ProductPriceBasePriceType::class, [
                'required' => false,
                'currency' => $options['currency']->getCode(),
                'scale' => $options['currency']->getSubUnits(),
            ])
            ->add('custom_price_rule', ProductPriceCustomPriceRuleType::class, [
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'customer_group_product_price';
    }

    public function getParent(): string
    {
        return ProductPriceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerGroupAwareInterface::class,
        ]);
    }
}
