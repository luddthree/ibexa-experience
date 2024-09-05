<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct as AttributeGroupCreateStructValue;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\AttributeGroup\AttributeGroupCreateStruct;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\AttributeGroup\AttributeGroupCreateStruct
 */
final class AttributeGroupCreateStructTest extends AbstractContextProviderTest
{
    private AttributeGroupCreateStructValue $object;

    public function setUp(): void
    {
        $this->object = new AttributeGroupCreateStructValue(
            'identifier'
        );
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new AttributeGroupCreateStruct();
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
