<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Currency;

use Ibexa\AdminUi\Form\Type\SwitcherType;
use Ibexa\Bundle\ProductCatalog\Form\Data\Currency\AbstractCurrencyData;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CurrencyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class)
            ->add('subunits', NumberType::class, [
                'label' => /** @Desc("Number of fractional places") */ 'form.currency.subunits',
            ])
            ->add('enabled', SwitcherType::class, [
                'label' => /** @Desc("Enable currency") */ 'form.currency.enabled',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AbstractCurrencyData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }
}
