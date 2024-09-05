<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\View;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\CustomerGroup\CustomerGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroup as LocalCustomerGroup;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\CustomerGroup\CustomerGroup
 */
final class CustomerGroupTest extends AbstractContextProviderTest
{
    private LocalCustomerGroup $object;

    public function setUp(): void
    {
        $this->object = new LocalCustomerGroup(
            1,
            'identifier',
            'name',
            'description',
            '1',
            []
        );
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new CustomerGroup();
    }

    protected function getPolicy(): PolicyInterface
    {
        return new View($this->object);
    }

    protected function getUnsupportedPolicy(): PolicyInterface
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface $customerGroup */
        $customerGroup = $this->createMock(CustomerGroupInterface::class);

        return new View($customerGroup);
    }

    protected function getExpectedContext(): ContextInterface
    {
        return new Context($this->object);
    }
}
