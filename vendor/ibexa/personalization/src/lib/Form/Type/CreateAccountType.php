<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Personalization\Form\Data\CreateAccountData;
use Ibexa\Personalization\Value\Customer\CustomerType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => /** @Desc("Account name") **/ 'account.create.name',
                    'required' => true,
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => /** @Desc("Type") **/ 'account.create.type',
                    'required' => true,
                    'choices' => [
                        /** @Desc("Commerce") **/
                        'account.type.commerce' => CustomerType::COMMERCE,
                        /** @Desc("Publisher") **/
                        'account.type.publisher' => CustomerType::PUBLISHER,
                    ],
                ]
            )
            ->add('next', SubmitType::class, [
                'label' => /** @Desc("Next") **/ 'account.create.next',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => CreateAccountData::class,
                'translation_domain' => 'ibexa_personalization',
            ]
        );
    }
}
