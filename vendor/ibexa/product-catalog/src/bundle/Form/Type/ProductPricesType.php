<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\Create\AbstractProductPriceCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\ProductPricesData;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductPricesType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $data = $form->getData();
        assert($data instanceof ProductPricesData);

        $view->vars['currency'] = $data->getPrice()->getCurrency();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', ProductPriceType::class, [
                'currency' => $options['currency'],
            ])
            ->add('customer_group_prices', CollectionType::class, [
                'entry_type' => CustomerGroupProductPriceType::class,
                'entry_options' => [
                    'currency' => $options['currency'],
                ],
                'block_prefix' => 'customer_group_product_prices',
            ])
        ;

        $builder->get('customer_group_prices')->addEventListener(FormEvents::SUBMIT, static function (FormEvent $event): void {
            $data = $event->getData();

            foreach ($data as $key => $customerGroupPrice) {
                if (!$customerGroupPrice instanceof AbstractProductPriceCreateData) {
                    continue;
                }

                if ($customerGroupPrice->getBasePrice() === null && $customerGroupPrice->getCustomPrice() === null) {
                    unset($data[$key]);
                }
            }

            $data = array_values($data);
            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductPricesData::class,
        ]);

        $resolver->setRequired('currency');
        $resolver->setAllowedTypes('currency', [
            CurrencyInterface::class,
        ]);
    }
}
