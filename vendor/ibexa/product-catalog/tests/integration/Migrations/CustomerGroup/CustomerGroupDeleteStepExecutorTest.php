<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupDeleteStep;
use Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupDeleteStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupDeleteStepExecutor
 */
final class CustomerGroupDeleteStepExecutorTest extends AbstractStepExecutorTest
{
    private CustomerGroupServiceInterface $customerGroupService;

    private CustomerGroupDeleteStepExecutor $executor;

    protected function setUp(): void
    {
        self::setAdministratorUser();

        $this->customerGroupService = self::getServiceByClassName(CustomerGroupServiceInterface::class);

        $this->executor = new CustomerGroupDeleteStepExecutor($this->customerGroupService);
        $this->configureExecutor($this->executor);
    }

    public function testCannotHandleOtherSteps(): void
    {
        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));
    }

    public function testHandle(): void
    {
        $customerGroup = $this->customerGroupService->getCustomerGroupByIdentifier('customer_group_1');
        self::assertNotNull($customerGroup);

        $step = new CustomerGroupDeleteStep(
            new FieldValueCriterion('identifier', 'customer_group_1'),
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $customerGroup = $this->customerGroupService->getCustomerGroupByIdentifier('customer_group_1');
        self::assertNull($customerGroup);
    }
}
