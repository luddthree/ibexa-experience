<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\CustomerGroup;

use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class CustomerGroupDeleteStepExecutor extends AbstractStepExecutor
{
    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(CustomerGroupServiceInterface $customerGroupService)
    {
        $this->customerGroupService = $customerGroupService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function doHandle(StepInterface $step)
    {
        assert($step instanceof CustomerGroupDeleteStep);

        $criteria = $step->getCriterion();

        $customerGroupQuery = new CustomerGroupQuery($criteria, [], null);
        $customerGroups = $this->customerGroupService->findCustomerGroups($customerGroupQuery);

        foreach ($customerGroups->getCustomerGroups() as $customerGroup) {
            $this->customerGroupService->deleteCustomerGroup($customerGroup);
        }

        return null;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof CustomerGroupDeleteStep;
    }
}
