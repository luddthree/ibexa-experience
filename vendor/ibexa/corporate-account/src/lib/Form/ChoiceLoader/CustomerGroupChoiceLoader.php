<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\ChoiceLoader;

use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class CustomerGroupChoiceLoader implements ChoiceLoaderInterface
{
    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(
        CustomerGroupServiceInterface $customerGroupService
    ) {
        $this->customerGroupService = $customerGroupService;
    }

    /**
     * @return array<string, int>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getChoiceList(): array
    {
        $customerGroupsList = $this->customerGroupService->findCustomerGroups(
            new CustomerGroupQuery(null, null, null)
        );

        $choices = [];

        foreach ($customerGroupsList->getCustomerGroups() as $customerGroup) {
            $choices[$customerGroup->getName()] = $customerGroup->getId();
        }

        return $choices;
    }

    public function loadChoiceList(callable $value = null): ChoiceListInterface
    {
        $choices = $this->getChoiceList();

        return new ArrayChoiceList($choices, $value);
    }

    public function loadChoicesForValues(
        array $values,
        callable $value = null
    ): array {
        $values = array_filter($values);

        if (null === $value) {
            return $values;
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    public function loadValuesForChoices(
        array $choices,
        callable $value = null
    ): array {
        $choices = array_filter($choices);

        if (null === $value) {
            return $choices;
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }
}
