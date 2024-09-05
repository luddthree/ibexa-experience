<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Personalization\Form\Data\UpdateInstallationKeyData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateInstallationKeyType extends AbstractType
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
            ->add('installationKey', InstallationKeyType::class, [
                'label' => $this->translator->trans(
                    /** @Desc("Installation key") */
                    'settings.form.installation_key',
                    [],
                    'ibexa_personalization'
                ),
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans(
                    /** @Desc("Save") */
                    'settings.form.submit',
                    [],
                    'ibexa_personalization'
                ),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', UpdateInstallationKeyData::class);
    }
}

class_alias(UpdateInstallationKeyType::class, 'Ibexa\Platform\Personalization\Form\Type\UpdateInstallationKeyType');
