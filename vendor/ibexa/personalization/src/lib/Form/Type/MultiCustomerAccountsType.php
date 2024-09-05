<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Personalization\Form\Data\MultiCustomerAccountsData;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MultiCustomerAccountsType extends AbstractType
{
    private SecurityServiceInterface $securityService;

    public function __construct(SecurityServiceInterface $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * @param array<mixed> $options
     * @phpstan-param FormBuilderInterface<\Symfony\Component\Form\FormBuilder> $builder
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('customer_id', ChoiceType::class, [
            'choices' => $this->getGrantedAccessChoices(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MultiCustomerAccountsData::class,
        ]);
    }

    /**
     * @return array<string|int>
     */
    public function getGrantedAccessChoices(): array
    {
        $choices = [];

        foreach ($this->securityService->getGrantedAccessList() as $customerId => $siteName) {
            $choices[$siteName] = $customerId;
        }

        return $choices;
    }
}

class_alias(MultiCustomerAccountsType::class, 'Ibexa\Platform\Personalization\Form\Type\MultiCustomerAccountsType');
