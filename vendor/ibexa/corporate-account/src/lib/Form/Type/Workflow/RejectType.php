<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Workflow;

use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\Type\Workflow\ChoiceLoader\RejectReasonsLoader;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RejectType extends AbstractType
{
    private CorporateAccountConfiguration $configuration;

    public function __construct(
        CorporateAccountConfiguration $configuration
    ) {
        $this->configuration = $configuration;
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('application', HiddenType::class)
            ->add('reason', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'choice_loader' => new RejectReasonsLoader($this->configuration),
                'label' => /** @Desc("Select reason") */ 'application.workflow.extra_actions.reason',
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
                'label' => /** @Desc("Notes") */ 'application.workflow.extra_actions.notes',
            ])
            ->add('reject', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'ibexa_corporate_account',
        ]);
    }
}
