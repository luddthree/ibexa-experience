<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Member;

use Ibexa\AdminUi\Form\Type\User\UserType;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\CorporateAccount\Form\Data\Member\MemberData;
use Ibexa\CorporateAccount\Form\DataTransformer\MemberTransformer;
use Ibexa\CorporateAccount\Form\Type\Company\CompanyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MemberType extends AbstractType
{
    private MemberService $memberService;

    public function __construct(
        MemberService $memberService
    ) {
        $this->memberService = $memberService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'user',
                UserType::class
            )
            ->add(
                'company',
                CompanyType::class
            );
        $builder->addViewTransformer(new MemberTransformer($this->memberService));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MemberData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
