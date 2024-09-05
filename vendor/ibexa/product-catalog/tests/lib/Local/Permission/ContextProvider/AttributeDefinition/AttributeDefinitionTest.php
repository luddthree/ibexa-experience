<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AttributeDefinition;

use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition\View;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\AttributeDefinition\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition as LocalAttributeDefinition;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\AttributeDefinition\AttributeDefinition
 */
final class AttributeDefinitionTest extends AbstractContextProviderTest
{
    private LocalAttributeDefinition $object;

    public function setUp(): void
    {
        $this->object = new LocalAttributeDefinition(
            1,
            'identifier',
            $this->createMock(AttributeTypeInterface::class),
            $this->createMock(AttributeGroupInterface::class),
            'name',
            0,
            [],
            '',
            [],
            [],
        );
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new AttributeDefinition();
    }

    protected function getPolicy(): PolicyInterface
    {
        return new View($this->object);
    }

    protected function getUnsupportedPolicy(): PolicyInterface
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface $attributeDefinition */
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        return new View($attributeDefinition);
    }

    protected function getExpectedContext(): ContextInterface
    {
        return new Context($this->object);
    }
}
