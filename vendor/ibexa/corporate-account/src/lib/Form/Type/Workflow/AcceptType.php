<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Workflow;

use Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroupChoiceType;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\Type\Workflow\ChoiceLoader\SalesRepLoader;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AcceptType extends AbstractType
{
    private const FIELD_APPLICATION = 'application';
    private const FIELD_CUSTOMER_GROUP = 'customer_group';
    private const FIELD_SALES_REP = 'sales_rep';
    private const FIELD_NOTES = 'notes';
    private const FIELD_ACCEPT = 'accept';

    private ContentService $contentService;

    private LocationService $locationService;

    private CorporateAccountConfiguration $configuration;

    public function __construct(
        ContentService $contentService,
        LocationService $locationService,
        CorporateAccountConfiguration $configuration
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->configuration = $configuration;
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add(self::FIELD_APPLICATION, HiddenType::class)
            ->add(self::FIELD_CUSTOMER_GROUP, CustomerGroupChoiceType::class, [
                'label' => /** @Desc("Select Customer group") */ 'application.workflow.extra_actions.approve.customer_group',
            ])
            ->add(self::FIELD_SALES_REP, ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'choice_loader' => new SalesRepLoader(
                    $this->configuration,
                    $this->contentService,
                    $this->locationService
                ),
                'label' => /** @Desc("Select Sales Representative") */ 'application.workflow.extra_actions.sales_rep',
            ])
            ->add(self::FIELD_NOTES, TextareaType::class, [
                'required' => false,
                'label' => /** @Desc("Notes") */ 'application.workflow.extra_actions.notes',
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $form = $event->getForm();

        /** @var \Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface $customerGroupsLoader */
        $customerGroupsLoader = $form
            ->get(self::FIELD_CUSTOMER_GROUP)
            ->getConfig()
            ->getOption('choice_loader');

        /** @var \Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface $customerGroupsLoader */
        $salesRepLoader = $form
            ->get(self::FIELD_SALES_REP)
            ->getConfig()
            ->getOption('choice_loader');

        $customerGroupChoices = $customerGroupsLoader->loadChoiceList()->getChoices();
        $salesRepoChoices = $salesRepLoader->loadChoiceList()->getChoices();

        $shouldBeDisabled = empty($customerGroupChoices) || empty($salesRepoChoices);
        $this->addAcceptSubmit($form, $shouldBeDisabled);
    }

    private function addAcceptSubmit(FormInterface $form, bool $shouldBeDisabled): void
    {
        $form->add(self::FIELD_ACCEPT, SubmitType::class, [
            'disabled' => $shouldBeDisabled,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'ibexa_corporate_account',
        ]);
    }
}
