<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceDecorator;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupListInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use PHPUnit\Framework\TestCase;

final class CustomerGroupServiceDecoratorTest extends TestCase
{
    private const EXAMPLE_IDENTIFIER = 'example';
    private const EXAMPLE_ID = 1;

    /** @var \Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CustomerGroupServiceInterface $service;

    private CustomerGroupServiceDecorator $decorator;

    protected function setUp(): void
    {
        $this->service = $this->createMock(CustomerGroupServiceInterface::class);
        $this->decorator = $this->createCustomerGroupServiceDecorator($this->service);
    }

    public function testCreateCustomerGroup(): void
    {
        $expectedCustomerGroup = $this->createMock(CustomerGroupInterface::class);
        $createStruct = new CustomerGroupCreateStruct('example', [], [], '0.0');

        $this->service
            ->expects(self::once())
            ->method('createCustomerGroup')
            ->with($createStruct)
            ->willReturn($expectedCustomerGroup);

        self::assertSame(
            $expectedCustomerGroup,
            $this->decorator->createCustomerGroup($createStruct)
        );
    }

    public function testDeleteCustomerGroup(): void
    {
        $customerGroup = $this->createMock(CustomerGroupInterface::class);

        $this->service
            ->expects(self::once())
            ->method('deleteCustomerGroup')
            ->with($customerGroup);

        $this->decorator->deleteCustomerGroup($customerGroup);
    }

    public function testGetCustomerGroupByIdentifier(): void
    {
        $expectedCustomerGroup = $this->createMock(CustomerGroupInterface::class);

        $this->service
            ->expects(self::once())
            ->method('getCustomerGroupByIdentifier')
            ->with(self::EXAMPLE_IDENTIFIER)
            ->willReturn($expectedCustomerGroup);

        self::assertSame(
            $expectedCustomerGroup,
            $this->decorator->getCustomerGroupByIdentifier(self::EXAMPLE_IDENTIFIER)
        );
    }

    public function testFindCustomerGroups(): void
    {
        $expectedCustomerGroupList = $this->createMock(CustomerGroupListInterface::class);
        $query = new CustomerGroupQuery();

        $this->service
            ->expects(self::once())
            ->method('findCustomerGroups')
            ->with($query)
            ->willReturn($expectedCustomerGroupList);

        self::assertSame(
            $expectedCustomerGroupList,
            $this->decorator->findCustomerGroups($query)
        );
    }

    public function testGetCustomerGroup(): void
    {
        $expectedCustomerGroup = $this->createMock(CustomerGroupInterface::class);

        $this->service
            ->expects(self::once())
            ->method('getCustomerGroup')
            ->with(self::EXAMPLE_ID)
            ->willReturn($expectedCustomerGroup);

        self::assertSame(
            $expectedCustomerGroup,
            $this->decorator->getCustomerGroup(self::EXAMPLE_ID)
        );
    }

    public function testUpdateCustomerGroup(): void
    {
        $expectedCustomerGroup = $this->createMock(CustomerGroupInterface::class);
        $updateStruct = new CustomerGroupUpdateStruct(self::EXAMPLE_ID);

        $this->service
            ->expects(self::once())
            ->method('updateCustomerGroup')
            ->with($updateStruct)
            ->willReturn($expectedCustomerGroup);

        self::assertSame(
            $expectedCustomerGroup,
            $this->decorator->updateCustomerGroup($updateStruct)
        );
    }

    private function createCustomerGroupServiceDecorator(
        CustomerGroupServiceInterface $service
    ): CustomerGroupServiceDecorator {
        return new class($service) extends CustomerGroupServiceDecorator {
            // Empty decorator implementation
        };
    }
}
