<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Personalization\Form\Data\AcceptanceData;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AcceptanceType extends AbstractType
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => $this->translator->trans(
                    /** @Desc("Full name") */
                    'activation_status.form.full_name',
                    [],
                    'ibexa_personalization'
                ),
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans(
                    /** @Desc("Email") */
                    'activation_status.form.email',
                    [],
                    'ibexa_personalization'
                ),
                'required' => true,
            ])
            ->add('installationKey', InstallationKeyType::class, [
                'label' => $this->translator->trans(
                    /** @Desc("Installation key") */
                    'settings.form.installation_key',
                    [],
                    'ibexa_personalization'
                ),
                'required' => true,
            ])
            ->add('termsAndConditions', CheckboxType::class, [
                'label' => $this->translator->trans(
                    /** @Desc("I have read and agree to the Terms and Conditions") */
                    'activation_status.form.terms_and_conditions',
                    [],
                    'ibexa_personalization'
                ),
                'required' => true,
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', AcceptanceData::class);
    }
}

class_alias(AcceptanceType::class, 'Ibexa\Platform\Personalization\Form\Type\AcceptanceType');
