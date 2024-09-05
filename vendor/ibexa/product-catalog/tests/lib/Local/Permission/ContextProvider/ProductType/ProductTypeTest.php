<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\ProductType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\View;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\ProductType\ProductType;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductType as LocalProductType;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\ProductType\ProductType
 */
final class ProductTypeTest extends AbstractContextProviderTest
{
    private LocalProductType $object;

    public function setUp(): void
    {
        $this->object = new LocalProductType(
            $this->createMock(ContentType::class)
        );
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new ProductType();
    }

    protected function getPolicy(): PolicyInterface
    {
        return new View($this->object);
    }

    protected function getUnsupportedPolicy(): PolicyInterface
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface $productType */
        $productType = $this->createMock(ProductTypeInterface::class);

        return new View($productType);
    }

    protected function getExpectedContext(): ContextInterface
    {
        return new Context($this->object->getContentType());
    }
}
