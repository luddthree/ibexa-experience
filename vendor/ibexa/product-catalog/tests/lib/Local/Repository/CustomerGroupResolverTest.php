<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value as CustomerGroupValue;
use Ibexa\ProductCatalog\Local\Repository\CustomerGroupResolver;
use PHPUnit\Framework\TestCase;

final class CustomerGroupResolverTest extends TestCase
{
    private const EXAMPLE_USER_ID = 1;
    private const EXAMPLE_CUSTOMER_GROUP_ID = 2;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver|\PHPUnit\Framework\MockObject\MockObject */
    private PermissionResolver $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject */
    private UserService $userService;

    /** @var \Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CustomerGroupServiceInterface $customerGroupService;

    private CustomerGroupResolver $customerGroupResolver;

    protected function setUp(): void
    {
        $this->permissionResolver = $this->createMock(PermissionResolver::class);
        $this->userService = $this->createMock(UserService::class);
        $this->customerGroupService = $this->createMock(CustomerGroupServiceInterface::class);
        $this->customerGroupResolver = new CustomerGroupResolver(
            $this->permissionResolver,
            $this->userService,
            $this->customerGroupService
        );
    }

    public function testResolveCustomerGroupForCurrentUser(): void
    {
        $userReference = $this->createMock(UserReference::class);
        $userReference->method('getUserId')->willReturn(self::EXAMPLE_USER_ID);

        $this->permissionResolver
            ->method('getCurrentUserReference')
            ->willReturn($userReference);

        $currencyUser = $this->createMock(User::class);
        $currencyUser->method('getFields')->willReturn(
            [
                new Field(['value' => new CustomerGroupValue(self::EXAMPLE_CUSTOMER_GROUP_ID)]),
            ]
        );

        $this->userService
            ->method('loadUser')
            ->with(self::EXAMPLE_USER_ID)
            ->willReturn($currencyUser);

        $expectedCustomerGroup = $this->createMock(CustomerGroupInterface::class);

        $this->customerGroupService
            ->method('getCustomerGroup')
            ->with(self::EXAMPLE_CUSTOMER_GROUP_ID)
            ->willReturn($expectedCustomerGroup);

        self::assertEquals(
            $expectedCustomerGroup,
            $this->customerGroupResolver->resolveCustomerGroup()
        );
    }

    public function testResolveCustomerGroupForGivenUser(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getFields')->willReturn(
            [
                new Field(['value' => new CustomerGroupValue(self::EXAMPLE_CUSTOMER_GROUP_ID)]),
            ]
        );

        $expectedCustomerGroup = $this->createMock(CustomerGroupInterface::class);

        $this->customerGroupService
            ->method('getCustomerGroup')
            ->with(self::EXAMPLE_CUSTOMER_GROUP_ID)
            ->willReturn($expectedCustomerGroup);

        self::assertEquals(
            $expectedCustomerGroup,
            $this->customerGroupResolver->resolveCustomerGroup($user)
        );
    }

    /**
     * @dataProvider dataProviderForResolveCustomerGroupFailure
     */
    public function testResolveCustomerGroupFailure(User $user): void
    {
        self:self::assertNull($this->customerGroupResolver->resolveCustomerGroup($user));
    }

    /**
     * @return iterable<string, array{\Ibexa\Contracts\Core\Repository\Values\User\User}>
     */
    public function dataProviderForResolveCustomerGroupFailure(): iterable
    {
        yield 'missing customer group field' => [
            $this->getUserWithFields([]),
        ];

        yield 'unassigned customer group' => [
            $this->getUserWithFields([
                new Field(['value' => new CustomerGroupValue(null)]),
            ]),
        ];
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field[] $fields
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getUserWithFields(array $fields): User
    {
        $user = $this->createMock(User::class);
        $user->method('getFields')->willReturn($fields);

        return $user;
    }
}
