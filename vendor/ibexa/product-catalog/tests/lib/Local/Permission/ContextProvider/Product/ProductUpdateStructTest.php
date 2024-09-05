<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\Product;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct as LocalProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Edit;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\Product\ProductUpdateStruct;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\Product\ProductUpdateStruct
 */
final class ProductUpdateStructTest extends AbstractContextProviderTest
{
    private LocalProductUpdateStruct $object;

    public function setUp(): void
    {
        $this->object = new LocalProductUpdateStruct(
            $this->createMock(ContentAwareProductInterface::class),
            $this->createMock(ContentUpdateStruct::class)
        );
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new ProductUpdateStruct();
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
        return new Context($this->object->getContentUpdateStruct());
    }
}
