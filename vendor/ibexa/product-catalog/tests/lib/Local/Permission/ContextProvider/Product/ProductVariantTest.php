<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\Product;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\View;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\Product\ProductVariant;
use Ibexa\ProductCatalog\Local\Repository\Values\Product as LocalProduct;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductVariant as LocalProductVariant;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\Product\ProductVariant
 */
final class ProductVariantTest extends AbstractContextProviderTest
{
    private LocalProductVariant $object;

    private Context $expectedContext;

    protected function setUp(): void
    {
        $content = $this->createMock(Content::class);

        $this->object = new LocalProductVariant(
            new LocalProduct(
                $this->createMock(ProductTypeInterface::class),
                $content,
                'base-code'
            ),
            'variant-code'
        );

        $this->expectedContext = new Context($content);
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new ProductVariant();
    }

    protected function getPolicy(): PolicyInterface
    {
        return new View($this->object);
    }

    protected function getUnsupportedPolicy(): PolicyInterface
    {
        return new View($this->createMock(ProductInterface::class));
    }

    protected function getExpectedContext(): ContextInterface
    {
        return $this->expectedContext;
    }
}
