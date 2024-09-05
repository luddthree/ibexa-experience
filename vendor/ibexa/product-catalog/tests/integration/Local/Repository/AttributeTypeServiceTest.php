<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Repository\AttributeTypeService;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\AttributeTypeService
 */
final class AttributeTypeServiceTest extends TestCase
{
    /** @var iterable<string,\Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface> */
    private iterable $types;

    public function setUp(): void
    {
        $this->types = [
            'foo' => new AttributeType('foo'),
            'bar' => new AttributeType('bar'),
            'baz' => new AttributeType('baz'),
        ];
    }

    public function testGetAttributeTypes(): void
    {
        $attributeTypeService = new AttributeTypeService($this->types);

        self::assertSame($this->types, $attributeTypeService->getAttributeTypes());
    }

    public function testGetAttributeType(): void
    {
        $attributeTypeService = new AttributeTypeService($this->types);

        self::assertSame(
            'foo',
            $attributeTypeService->getAttributeType('foo')->getIdentifier()
        );
    }

    public function testGetAttributeTypeWillThrowNotFoundException(): void
    {
        $attributeTypeService = new AttributeTypeService($this->types);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            sprintf("Could not find '%s' with identifier 'non-existent'", AttributeTypeInterface::class)
        );

        $attributeTypeService->getAttributeType('non-existent');
    }

    public function testHasAttributeType(): void
    {
        $attributeTypeService = new AttributeTypeService($this->types);

        self::assertTrue(
            $attributeTypeService->hasAttributeType('foo')
        );

        self::assertFalse(
            $attributeTypeService->hasAttributeType('non-existent')
        );
    }
}
