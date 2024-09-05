<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Values;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\Values\ProductUpdateStruct
 */
final class ProductUpdateStructTest extends TestCase
{
    public function testProhibitsSettingOfProductSpecificationTypeFields(): void
    {
        $struct = new ProductUpdateStruct(
            $this->createMock(ContentAwareProductInterface::class),
            $this->createMock(ContentUpdateStruct::class),
        );

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Indirect modification of product specification is not allowed');
        $struct->setField('product_field_identifier', new ProductSpecification\Value());
    }

    public function testSetFieldProxiesToInternalContentStruct(): void
    {
        $contentUpdateStruct = $this->createMock(ContentUpdateStruct::class);
        $contentUpdateStruct
            ->expects(self::once())
            ->method('setField')
            ->with('some_field', 'foo');

        $struct = new ProductUpdateStruct(
            $this->createMock(ContentAwareProductInterface::class),
            $contentUpdateStruct,
        );

        $struct->setField('some_field', 'foo');
    }
}
