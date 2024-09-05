<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\CustomerGroupDetailedView;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Core\Repository\Values\User\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\View\CustomerGroupDetailedView
 */
final class CustomerGroupDetailedViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $expectedCustomerGroup = $this->createMock(CustomerGroupInterface::class);
        $expectedUsers = [
            $this->createMock(User::class),
            $this->createMock(User::class),
            $this->createMock(User::class),
        ];

        $view = new CustomerGroupDetailedView(
            'example.html.twig',
            $expectedCustomerGroup,
            $expectedUsers
        );

        self::assertEquals([
            'customer_group' => $expectedCustomerGroup,
            'users' => $expectedUsers,
        ], $view->getParameters());
    }
}
