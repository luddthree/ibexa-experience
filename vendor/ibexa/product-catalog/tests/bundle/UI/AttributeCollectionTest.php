<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\UI;

use Ibexa\Bundle\ProductCatalog\UI\AttributeCollection;
use Ibexa\Bundle\ProductCatalog\UI\AttributeGroup;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use PHPUnit\Framework\TestCase;

final class AttributeCollectionTest extends TestCase
{
    public function testCreateFromProduct(): void
    {
        $groupFoo = $this->createAttributeGroup('foo');
        $groupBar = $this->createAttributeGroup('bar');

        $attributeA = $this->createAttributeWithGroup($groupFoo);
        $attributeB = $this->createAttributeWithGroup($groupFoo);
        $attributeC = $this->createAttributeWithGroup($groupFoo);

        $attributeX = $this->createAttributeWithGroup($groupBar);
        $attributeY = $this->createAttributeWithGroup($groupBar);
        $attributeZ = $this->createAttributeWithGroup($groupBar);

        $product = $this->createMock(ProductInterface::class);
        $product->method('getAttributes')->willReturn([
            $attributeA, $attributeB, $attributeC, $attributeX, $attributeY, $attributeZ,
        ]);

        self::assertEquals(
            new AttributeCollection([
                'foo' => new AttributeGroup($groupFoo, [$attributeA, $attributeB, $attributeC]),
                'bar' => new AttributeGroup($groupBar, [$attributeX, $attributeY, $attributeZ]),
            ]),
            AttributeCollection::createFromProduct($product)
        );
    }

    private function createAttributeGroup(string $identifier): AttributeGroupInterface
    {
        $group = $this->createMock(AttributeGroupInterface::class);
        $group->method('getIdentifier')->willReturn($identifier);

        return $group;
    }

    private function createAttributeWithGroup(AttributeGroupInterface $group): AttributeInterface
    {
        $definition = $this->createMock(AttributeDefinitionInterface::class);
        $definition->method('getGroup')->willReturn($group);

        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getAttributeDefinition')->willReturn($definition);

        return $attribute;
    }
}
