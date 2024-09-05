<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CustomerGroupValueTransformer;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerGroupChoiceType extends AbstractType
{
    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(CustomerGroupServiceInterface $customerGroupService)
    {
        $this->customerGroupService = $customerGroupService;
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CustomerGroupValueTransformer($this->customerGroupService));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_label' => static function (?CustomerGroupInterface $value): string {
                if ($value === null) {
                    return '';
                }

                return $value->getName();
            },
            'choice_value' => static function (?CustomerGroupInterface $customerGroup) {
                if ($customerGroup === null) {
                    return '';
                }

                return $customerGroup->getId();
            },
            'choice_loader' => ChoiceList::lazy($this, $this->getChoiceLoader()),
        ]);
    }

    /**
     * @phpstan-return callable(): iterable<\Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface> $choiceLoader
     */
    private function getChoiceLoader(): callable
    {
        return function (): iterable {
            return $this->customerGroupService->findCustomerGroups(
                new CustomerGroupQuery(null, null, null)
            );
        };
    }
}
