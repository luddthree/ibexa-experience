<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupUpdateData;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class UniqueCustomerGroupIdentifierValidator extends ConstraintValidator
{
    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(CustomerGroupServiceInterface $customerGroupService)
    {
        $this->customerGroupService = $customerGroupService;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCustomerGroupIdentifier $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof CustomerGroupCreateData && !$value instanceof CustomerGroupUpdateData) {
            return;
        }

        if (empty($value->getIdentifier())) {
            return;
        }

        $customerGroup = $this->customerGroupService->getCustomerGroupByIdentifier($value->getIdentifier());

        if ($customerGroup === null || $this->isSameCustomerGroup($value, $customerGroup)) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->atPath('identifier')
            ->setParameter('%identifier%', $value->getIdentifier())
            ->addViolation();
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupCreateData|\Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupUpdateData $value
     */
    private function isSameCustomerGroup($value, CustomerGroupInterface $customerGroup): bool
    {
        if ($value instanceof CustomerGroupUpdateData) {
            return $value->getId() === $customerGroup->getId();
        }

        return false;
    }
}
