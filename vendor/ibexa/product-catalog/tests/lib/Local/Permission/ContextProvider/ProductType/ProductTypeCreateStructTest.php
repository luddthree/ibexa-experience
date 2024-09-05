<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\ProductType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeCreateStruct as LocalProductTypeCreateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Create;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\ProductType\ProductTypeCreateStruct;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\ProductType\ProductTypeCreateStruct
 */
final class ProductTypeCreateStructTest extends AbstractContextProviderTest
{
    private LocalProductTypeCreateStruct $object;

    public function setUp(): void
    {
        $this->object = new LocalProductTypeCreateStruct(
            $this->createMock(ContentTypeCreateStruct::class),
            'eng-GB',
            []
        );
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new ProductTypeCreateStruct();
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
        return new Context($this->object->getContentTypeCreateStruct());
    }
}
