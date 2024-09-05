<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Personalization\Event\Subscriber;

use Ibexa\Contracts\ProductCatalog\Events\CreatePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\DeletePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\ExecutePriceStructsEvent;
use Ibexa\Contracts\ProductCatalog\Events\UpdatePriceEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDeleteBaseProductVariantsEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Delete;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductVariantList;
use Ibexa\ProductCatalog\Personalization\Event\Subscriber\ProductEventSubscriber;
use Money\Currency;
use Money\Money;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProductEventSubscriberTest extends AbstractCoreEventSubscriberTest
{
    private ProductEventSubscriber $productEventSubscriber;

    public function setUp(): void
    {
        parent::setUp();

        $this->productEventSubscriber = new ProductEventSubscriber(
            $this->contentServiceMock,
            $this->productServiceMock,
            $this->apiProductServiceMock,
            $this->permissionResolver
        );
    }

    public function getEventSubscriber(): EventSubscriberInterface
    {
        return $this->productEventSubscriber;
    }

    /**
     * @return iterable<int, array<int, string>>
     */
    public function subscribedEventsDataProvider(): iterable
    {
        return [
            [CreatePriceEvent::class],
            [UpdatePriceEvent::class],
            [DeletePriceEvent::class],
            [ExecutePriceStructsEvent::class],
        ];
    }

    public function testCallOnCreatePriceMethod(): void
    {
        $product = $this->getProductMock();
        $price = $this->createMock(PriceInterface::class);

        $createStruct = new ProductPriceCreateStruct(
            $product,
            $this->createMock(CurrencyInterface::class),
            new Money('10', new Currency('foo')),
            null,
            null
        );

        $event = new CreatePriceEvent($createStruct, $price);
        $productEventSubscriber = $this->getProductEventSubscriber(
            'updateContent',
            $this->content
        );
        $productEventSubscriber->onCreatePrice($event);
    }

    public function testCallOnUpdatePriceMethod(): void
    {
        $product = $this->getProductMock();
        $price = $this->createMock(PriceInterface::class);

        $price
            ->expects(self::once())
            ->method('getProduct')
            ->willReturn($product);

        $updateStruct = new ProductPriceUpdateStruct($price);

        $event = new UpdatePriceEvent($price, $updateStruct);

        $productEventSubscriber = $this->getProductEventSubscriber(
            'updateContent',
            $this->content
        );
        $productEventSubscriber->onUpdatePrice($event);
    }

    public function testCallOnDeletePriceMethod(): void
    {
        $price = $this->createMock(PriceInterface::class);

        $deleteStruct = new ProductPriceDeleteStruct($price);
        $event = new DeletePriceEvent($deleteStruct);

        $productEventSubscriber = $this->getProductEventSubscriber(
            'updateContent',
            $this->content
        );
        $productEventSubscriber->onDeletePrice($event);
    }

    public function testCallOnUpdateProductForBaseProduct(): void
    {
        $product = $this->getProductMock('code', 0, true);
        $updateStruct = new ProductUpdateStruct($product, new ContentUpdateStruct());
        $event = new UpdateProductEvent($product, $updateStruct);

        $productVariant = $this->createMock(ProductVariantInterface::class);

        $this->apiProductServiceMock
            ->method('findProductVariants')
            ->willReturn(
                new ProductVariantList(
                    [
                        $productVariant,
                    ],
                    1
                )
            );

        $this->productServiceMock
            ->method('updateVariants')
            ->with(
                [
                    $productVariant,
                ]
            );

        $productEventSubscriber = new ProductEventSubscriber(
            $this->contentServiceMock,
            $this->productServiceMock,
            $this->apiProductServiceMock,
            $this->permissionResolver
        );

        $productEventSubscriber->onUpdateProduct($event);
    }

    public function testCallOnUpdateProduct(): void
    {
        $product = $this->getProductMock('code', 0);
        $updateStruct = new ProductUpdateStruct($product, new ContentUpdateStruct());
        $event = new UpdateProductEvent($product, $updateStruct);

        $this->apiProductServiceMock
            ->expects(self::never())
            ->method('findProductVariants');

        $this->productServiceMock
            ->expects(self::never())
            ->method('updateVariants');

        $productEventSubscriber = new ProductEventSubscriber(
            $this->contentServiceMock,
            $this->productServiceMock,
            $this->apiProductServiceMock,
            $this->permissionResolver
        );

        $productEventSubscriber->onUpdateProduct($event);
    }

    public function testCallOnBeforeDeleteBaseProductVariantsWithoutPermission(): void
    {
        $product = $this->getProductMock('code', 0, true);
        $event = new BeforeDeleteBaseProductVariantsEvent($product);

        $this->permissionResolver
            ->method('canUser')
            ->with(new Delete($product))
            ->willReturn(false);

        $this->apiProductServiceMock
            ->expects(self::never())
            ->method('findProductVariants');

        $this->productServiceMock
            ->expects(self::never())
            ->method('updateVariants');

        $productEventSubscriber = new ProductEventSubscriber(
            $this->contentServiceMock,
            $this->productServiceMock,
            $this->apiProductServiceMock,
            $this->permissionResolver
        );

        $productEventSubscriber->onBeforeDeleteBaseProductVariants($event);
    }

    public function testCallOnBeforeDeleteBaseProductVariantsWithNonBaseProduct(): void
    {
        $product = $this->getProductMock('code', 0);
        $event = new BeforeDeleteBaseProductVariantsEvent($product);

        $this->permissionResolver
            ->method('canUser')
            ->with(new Delete($product))
            ->willReturn(true);

        $this->apiProductServiceMock
            ->expects(self::never())
            ->method('findProductVariants');

        $this->productServiceMock
            ->expects(self::never())
            ->method('updateVariants');

        $productEventSubscriber = new ProductEventSubscriber(
            $this->contentServiceMock,
            $this->productServiceMock,
            $this->apiProductServiceMock,
            $this->permissionResolver
        );

        $productEventSubscriber->onBeforeDeleteBaseProductVariants($event);
    }

    public function testCallOnBeforeDeleteBaseProductVariants(): void
    {
        $product = $this->getProductMock('code', 0);
        $event = new BeforeDeleteBaseProductVariantsEvent($product);

        $this->permissionResolver
            ->method('canUser')
            ->with(new Delete($product))
            ->willReturn(true);

        $productVariant = $this->createMock(ProductVariantInterface::class);

        $this->apiProductServiceMock
            ->method('findProductVariants')
            ->willReturn(
                new ProductVariantList(
                    [
                        $productVariant,
                    ],
                    1
                )
            );

        $this->productServiceMock
            ->method('deleteVariants')
            ->with(
                [
                    $productVariant,
                ]
            );

        $productEventSubscriber = new ProductEventSubscriber(
            $this->contentServiceMock,
            $this->productServiceMock,
            $this->apiProductServiceMock,
            $this->permissionResolver
        );

        $productEventSubscriber->onBeforeDeleteBaseProductVariants($event);
    }

    public function testCallExecutePriceStructsMethod(): void
    {
        $productPriceStruct1 = $this->getProductPriceStruct('foo');
        $productPriceStruct2 = $this->getProductPriceStruct('foo', 0);
        $productPriceStruct3 = $this->getProductPriceStruct('bar');

        $priceStructs = [
            $productPriceStruct1,
            $productPriceStruct2,
            $productPriceStruct3,
        ];

        $event = new ExecutePriceStructsEvent($priceStructs);

        $productEventSubscriber = $this->getProductEventSubscriber(
            'updateContentItems',
            [
                $this->getProductMock()->getContent(),
                $this->getProductMock()->getContent(),
            ]
        );
        $productEventSubscriber->onExecutePriceStructs($event);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content|array<
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Content
     * > $arguments
     */
    private function getProductEventSubscriber(
        string $method,
        $arguments
    ): ProductEventSubscriber {
        $this->contentServiceMock
            ->method($method)
            ->with($arguments)
            ->willReturnSelf();

        return new ProductEventSubscriber(
            $this->contentServiceMock,
            $this->productServiceMock,
            $this->apiProductServiceMock,
            $this->permissionResolver
        );
    }

    private function getProductPriceStruct(string $productCode, int $getContentCount = 1): ProductPriceStructInterface
    {
        $productPriceStruct = $this->createMock(ProductPriceStructInterface::class);
        $productPriceStruct
            ->method('getProduct')
            ->willReturn($this->getProductMock($productCode, $getContentCount));

        return $productPriceStruct;
    }

    private function getProductMock(
        string $productCode = 'code',
        int $getContentCount = 1,
        bool $isBaseProduct = false
    ): ContentAwareProductInterface {
        $product = $this->createMock(ContentAwareProductInterface::class);
        $product
            ->expects(self::exactly($getContentCount))
            ->method('getContent')
            ->willReturn($this->content);
        $product
            ->method('getCode')
            ->willReturn($productCode);
        $product
            ->method('isBaseProduct')
            ->willReturn($isBaseProduct);

        return $product;
    }
}
