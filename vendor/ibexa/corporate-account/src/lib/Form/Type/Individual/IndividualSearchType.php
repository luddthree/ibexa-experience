<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Individual;

use Ibexa\CorporateAccount\Form\ChoiceLoader\CustomerGroupChoiceLoader;
use Ibexa\CorporateAccount\Form\Data\CompanySearchQueryData;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IndividualSearchType extends AbstractType
{
    private CustomerGroupChoiceLoader $customerGroupChoiceLoader;

    public function __construct(
        CustomerGroupChoiceLoader $customerGroupChoiceLoader
    ) {
        $this->customerGroupChoiceLoader = $customerGroupChoiceLoader;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', TextType::class, [
                'required' => false,
            ])
            ->add('customer_group', ChoiceType::class, [
                'choice_loader' => $this->customerGroupChoiceLoader,
                'label' => /** @Desc("Customer group") */ 'corporate_account.individual_search.customer_group.label',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    /** @Desc("Active") */
                    'corporate_account.individual_search.status.choice.active' => true,
                    /** @Desc("De-activated") */
                    'corporate_account.individual_search.status.choice.deactivated' => false,
                ],
                'label' => /** @Desc("Status") */ 'corporate_account.individual_search.status.label',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompanySearchQueryData::class,
            'translation_domain' => 'ibexa_corporate_account',
        ]);
    }
}
