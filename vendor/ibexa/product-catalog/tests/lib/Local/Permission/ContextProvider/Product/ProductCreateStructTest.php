<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\Product;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct as LocalProductCreateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Create;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\Product\ProductCreateStruct;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\Product\ProductCreateStruct
 */
final class ProductCreateStructTest extends AbstractContextProviderTest
{
    private LocalProductCreateStruct $object;

    public function setUp(): void
    {
        $this->object = new LocalProductCreateStruct(
            $this->createMock(ProductTypeInterface::class),
            $this->createMock(ContentCreateStruct::class),
            'code'
        );
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new ProductCreateStruct();
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
        return new Context($this->object->getContentCreateStruct());
    }
}
