<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\View;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\AttributeGroup\AttributeGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup as LocalAttributeGroup;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\AttributeGroup\AttributeGroup
 */
final class AttributeGroupTest extends AbstractContextProviderTest
{
    private LocalAttributeGroup $object;

    public function setUp(): void
    {
        $this->object = new LocalAttributeGroup(1, 'identifier', 'name', 0, [], []);
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new AttributeGroup();
    }

    protected function getPolicy(): PolicyInterface
    {
        return new View($this->object);
    }

    protected function getUnsupportedPolicy(): PolicyInterface
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface $attributeGroup */
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);

        return new View($attributeGroup);
    }

    protected function getExpectedContext(): ContextInterface
    {
        return new Context($this->object);
    }
}
