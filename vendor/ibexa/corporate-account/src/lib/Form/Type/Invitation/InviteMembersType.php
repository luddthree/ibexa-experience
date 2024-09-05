<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Invitation;

use Ibexa\CorporateAccount\Form\Data\Invitation\InviteMembersData;
use Ibexa\CorporateAccount\Form\Data\Invitation\SimpleInvitationData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InviteMembersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'invitations',
                CollectionType::class,
                [
                    'entry_type' => SimpleInvitationType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'data' => [new SimpleInvitationData()],
                    'label' => false,
                    'entry_options' => [
                        'label' => false,
                    ],
                ]
            )
            ->add(
                'send',
                SubmitType::class,
                [
                    'label' => /** @Desc("Send") */ 'corporate_account.invite_members.submit',
                    'attr' => [
                        'class' => 'ibexa-btn ibexa-btn--primary',
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InviteMembersData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
