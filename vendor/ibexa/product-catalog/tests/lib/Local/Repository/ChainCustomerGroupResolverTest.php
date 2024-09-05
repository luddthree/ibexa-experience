<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\ProductCatalog\Local\Repository\ChainCustomerGroupResolver;
use PHPUnit\Framework\TestCase;

final class ChainCustomerGroupResolverTest extends TestCase
{
    public function testResolveCustomerGroupNoResolvers(): void
    {
        $resolver = new ChainCustomerGroupResolver();

        self::assertNull($resolver->resolveCustomerGroup());
    }

    public function testResolveCustomerGroupNextNotCalled(): void
    {
        $resolver1 = $this->createMock(CustomerGroupResolverInterface::class);
        $resolver1
            ->expects(self::once())
            ->method('resolveCustomerGroup')
            ->willReturn($this->createMock(CustomerGroupInterface::class));

        $resolver2 = $this->createMock(CustomerGroupResolverInterface::class);

        $resolver2
            ->expects(self::never())
            ->method('resolveCustomerGroup');

        $resolver = new ChainCustomerGroupResolver([$resolver1, $resolver2]);

        self::assertInstanceOf(CustomerGroupInterface::class, $resolver->resolveCustomerGroup());
    }

    public function testResolveCustomerGroupNextIsCalled(): void
    {
        $resolver1 = $this->createMock(CustomerGroupResolverInterface::class);
        $resolver1
            ->expects(self::once())
            ->method('resolveCustomerGroup')
            ->willReturn(null);

        $resolver2 = $this->createMock(CustomerGroupResolverInterface::class);

        $resolver2
            ->expects(self::once())
            ->method('resolveCustomerGroup')
            ->willReturn($this->createMock(CustomerGroupInterface::class));

        $resolver = new ChainCustomerGroupResolver([$resolver1, $resolver2]);

        self::assertInstanceOf(CustomerGroupInterface::class, $resolver->resolveCustomerGroup());
    }
}
