<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct as CustomerGroupCreateStructValue;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\CustomerGroup\CustomerGroupCreateStruct
 */
final class CustomerGroupCreateStructTest extends AbstractContextProviderTest
{
    private CustomerGroupCreateStructValue $object;

    public function setUp(): void
    {
        $this->object = new CustomerGroupCreateStructValue(
            'identifier',
            [],
            [],
            '1'
        );
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new CustomerGroupCreateStruct();
    }

    protected function getPolicy(): PolicyInterface
    {
        return new Create($this->object);
    }

    protected function getUnsupportedPolicy(): PolicyInterface
    {
        return new Create();
    }

    protected function getExpectedContext(): ContextInterface
    {
        return new Context($this->object);
    }
}
