<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\ProductType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeUpdateStruct as LocalProductTypeUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Edit;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\ProductType\ProductTypeUpdateStruct;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\Product\ProductUpdateStruct
 */
final class ProductTypeUpdateStructTest extends AbstractContextProviderTest
{
    private LocalProductTypeUpdateStruct $object;

    public function setUp(): void
    {
        $this->object = new LocalProductTypeUpdateStruct(
            $this->createMock(ProductTypeInterface::class),
            $this->createMock(ContentTypeUpdateStruct::class)
        );
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new ProductTypeUpdateStruct();
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
        return new Context($this->object->getContentTypeUpdateStruct());
    }
}
