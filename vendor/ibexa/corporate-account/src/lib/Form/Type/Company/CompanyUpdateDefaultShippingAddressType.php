<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Company;

use Ibexa\AdminUi\Form\Type\Content\ContentInfoType;
use Ibexa\CorporateAccount\Form\Data\Company\CompanyUpdateDefaultShippingAddressData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CompanyUpdateDefaultShippingAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'company',
                ContentInfoType::class,
                ['label' => false]
            )
            ->add(
                'address',
                ContentInfoType::class,
                ['attr' => ['hidden' => true], 'label' => false]
            )
            ->add(
                'update',
                SubmitType::class,
                ['attr' => ['hidden' => true]]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompanyUpdateDefaultShippingAddressData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
