<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\Edit;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct as CustomerGroupUpdateStructValue;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\CustomerGroup\CustomerGroupUpdateStruct;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\CustomerGroup\CustomerGroupUpdateStruct
 */
final class CustomerGroupUpdateStructTest extends AbstractContextProviderTest
{
    private CustomerGroupUpdateStructValue $object;

    public function setUp(): void
    {
        $this->object = new CustomerGroupUpdateStructValue(
            1,
            'identifier',
            [],
            [],
            '1'
        );
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new CustomerGroupUpdateStruct();
    }

    protected function getPolicy(): PolicyInterface
    {
        return new Edit($this->object);
    }

    protected function getUnsupportedPolicy(): PolicyInterface
    {
        return new Edit();
    }

    protected function getExpectedContext(): ContextInterface
    {
        return new Context($this->object);
    }
}
