<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupUpdateStruct as AttributeGroupUpdateStructValue;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\Edit;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\AttributeGroup\AttributeGroupUpdateStruct;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\AttributeGroup\AttributeGroupUpdateStruct
 */
final class AttributeGroupUpdateStructTest extends AbstractContextProviderTest
{
    private AttributeGroupUpdateStructValue $object;

    public function setUp(): void
    {
        $this->object = new AttributeGroupUpdateStructValue();
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new AttributeGroupUpdateStruct();
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
