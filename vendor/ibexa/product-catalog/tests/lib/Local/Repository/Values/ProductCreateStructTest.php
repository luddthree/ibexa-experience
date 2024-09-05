<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Values;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct
 */
final class ProductCreateStructTest extends TestCase
{
    public function testProhibitsSettingOfProductSpecificationTypeFields(): void
    {
        $struct = new ProductCreateStruct(
            $this->createMock(ProductTypeInterface::class),
            $this->createMock(ContentCreateStruct::class),
            'product_field_identifier'
        );

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Indirect modification of product specification is not allowed');
        $struct->setField('product_field_identifier', new ProductSpecification\Value());
    }

    public function testSetFieldProxiesToInternalContentStruct(): void
    {
        $contentCreateStruct = $this->createMock(ContentCreateStruct::class);
        $contentCreateStruct
            ->expects(self::once())
            ->method('setField')
            ->with('some_field', 'foo');

        $struct = new ProductCreateStruct(
            $this->createMock(ProductTypeInterface::class),
            $contentCreateStruct,
            'product_field_identifier'
        );

        $struct->setField('some_field', 'foo');
    }
}
