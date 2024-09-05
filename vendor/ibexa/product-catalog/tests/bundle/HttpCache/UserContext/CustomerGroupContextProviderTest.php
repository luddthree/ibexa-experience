<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\HttpCache\UserContext;

use FOS\HttpCache\UserContext\UserContext;
use Ibexa\Bundle\ProductCatalog\HttpCache\UserContext\CustomerGroupContextProvider;
use Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use PHPUnit\Framework\TestCase;

final class CustomerGroupContextProviderTest extends TestCase
{
    private const EXAMPLE_CUSTOMER_GROUP_IDENTIFIER = 'standard';

    /** @var \Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private CustomerGroupResolverInterface $customerGroupResolver;

    private CustomerGroupContextProvider $customerGroupContextProvider;

    protected function setUp(): void
    {
        $this->customerGroupResolver = $this->createMock(CustomerGroupResolverInterface::class);
        $this->customerGroupContextProvider = new CustomerGroupContextProvider($this->customerGroupResolver);
    }

    public function testUpdateUserContext(): void
    {
        $customerGroup = $this->createCustomerGroupWithIdentifier(self::EXAMPLE_CUSTOMER_GROUP_IDENTIFIER);

        $this->customerGroupResolver
            ->method('resolveCustomerGroup')
            ->willReturn($customerGroup);

        $context = new UserContext();

        $this->customerGroupContextProvider->updateUserContext($context);

        self::assertEquals([
            'customer_group' => self::EXAMPLE_CUSTOMER_GROUP_IDENTIFIER,
        ], $context->getParameters());
    }

    public function testUpdateUserContextWithoutCustomerGroup(): void
    {
        $this->customerGroupResolver
            ->method('resolveCustomerGroup')
            ->willReturn(null);

        $context = new UserContext();

        $this->customerGroupContextProvider->updateUserContext($context);

        self::assertFalse($context->hasParameter('customer_group'));
    }

    private function createCustomerGroupWithIdentifier(string $identifier): CustomerGroupInterface
    {
        $group = $this->createMock(CustomerGroupInterface::class);
        $group->method('getIdentifier')->willReturn($identifier);

        return $group;
    }
}
