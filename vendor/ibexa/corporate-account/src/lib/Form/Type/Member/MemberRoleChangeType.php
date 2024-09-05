<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Member;

use Ibexa\CorporateAccount\Form\Data\Member\MemberRoleChangeData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberRoleChangeType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add(
                'new_role',
                MemberRoleChoiceType::class,
                [
                    'label' => /** @Desc("Select new role") */ 'corporate_account.member.corporate_role_change.new_role',
                ],
            )
            ->add(
                'member',
                MemberType::class
            )
            ->add(
                'change',
                SubmitType::class,
                [
                    'label' => /** @Desc("Save") */ 'corporate_account.member.corporate_role_change.submit',
                ],
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MemberRoleChangeData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
