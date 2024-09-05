<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Workflow;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\Type\Workflow\ChoiceLoader\OnHoldReasonsLoader;
use Ibexa\CorporateAccount\Form\Type\Workflow\ChoiceLoader\SalesRepLoader;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OnHoldType extends AbstractType
{
    private CorporateAccountConfiguration $configuration;

    private ContentService $contentService;

    private LocationService $locationService;

    public function __construct(
        CorporateAccountConfiguration $configuration,
        ContentService $contentService,
        LocationService $locationService
    ) {
        $this->contentService = $contentService;
        $this->configuration = $configuration;
        $this->locationService = $locationService;
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('application', HiddenType::class)
            ->add('sales_rep', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'choice_loader' => new SalesRepLoader(
                    $this->configuration,
                    $this->contentService,
                    $this->locationService
                ),
                'label' => /** @Desc("Select Sales Representative") */ 'application.workflow.extra_actions.sales_rep',
            ])
            ->add('reason', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'choice_loader' => new OnHoldReasonsLoader($this->configuration),
                'label' => /** @Desc("Select reason") */ 'application.workflow.extra_actions.reason',
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
                'label' => /** @Desc("Notes") */ 'application.workflow.extra_actions.notes',
            ])
            ->add('on_hold', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'ibexa_corporate_account',
        ]);
    }
}
