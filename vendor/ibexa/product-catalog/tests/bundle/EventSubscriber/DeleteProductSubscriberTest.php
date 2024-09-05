<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\DeleteProductSubscriber;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeDeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceListInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\Repository\ContentService;
use Ibexa\ProductCatalog\Local\Repository\Values\PriceList;
use Ibexa\ProductCatalog\Local\Repository\Values\Product;
use PHPUnit\Framework\TestCase;

final class DeleteProductSubscriberTest extends TestCase
{
    private const CONTENT_ID = 43;
    private const PRODUCT_CODE = 'product_code';

    /** @var \Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $priceService;

    /** @var \Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $availabilityService;

    /** @var \Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $productService;

    /** @var \Ibexa\Core\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject */
    private $contentService;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\PriceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $priceA;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\PriceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $priceB;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content|\PHPUnit\Framework\MockObject\MockObject */
    private $content;

    private ProductInterface $product;

    private DeleteProductSubscriber $subscriber;

    private BeforeDeleteContentEvent $event;

    protected function setUp(): void
    {
        $this->priceService = $this->createMock(ProductPriceServiceInterface::class);
        $this->availabilityService = $this->createMock(ProductAvailabilityServiceInterface::class);
        $this->productService = $this->createMock(LocalProductServiceInterface::class);
        $this->contentService = $this->createMock(ContentService::class);

        $contentInfo = new ContentInfo(['id' => self::CONTENT_ID]);
        $this->event = new BeforeDeleteContentEvent($contentInfo);
        $this->content = $this->createMock(Content::class);

        $this->product = $this->getProduct($this->content);

        $this->priceA = $this->createMock(PriceInterface::class);
        $this->priceB = $this->createMock(PriceInterface::class);

        $this->subscriber = new DeleteProductSubscriber(
            $this->priceService,
            $this->availabilityService,
            $this->productService,
            $this->contentService,
        );
    }

    public function testOnBeforeDeleteContentWhenProduct(): void
    {
        $this->contentService
            ->expects(self::once())
            ->method('loadContent')
            ->willReturn($this->content);

        $this->productService
            ->expects(self::once())
            ->method('isProduct')
            ->with($this->content)
            ->willReturn(true);

        $this->productService
            ->expects(self::once())
            ->method('getProductFromContent')
            ->with($this->content)
            ->willReturn($this->product);

        $this->priceService
            ->expects(self::once())
            ->method('findPricesByProductCode')
            ->with(self::PRODUCT_CODE)
            ->willReturn($this->getPriceList(
                [
                    $this->priceA,
                    $this->priceB,
                ]
            ));

        $this->priceService
            ->expects(self::once())
            ->method('findPricesByProductCode')
            ->with(self::PRODUCT_CODE)
            ->willReturn($this->getPriceList(
                [
                    $this->priceA,
                    $this->priceB,
                ]
            ));

        $this->priceService
            ->method('deletePrice')
            ->withConsecutive(
                [new ProductPriceDeleteStruct($this->priceA)],
                [new ProductPriceDeleteStruct($this->priceB)],
            );

        $this->availabilityService
            ->expects(self::once())
            ->method('deleteProductAvailability')
            ->with($this->product);

        $this->subscriber->onBeforeDeleteContent($this->event);
    }

    public function testOnBeforeDeleteContentWithoutProduct(): void
    {
        $this->contentService
            ->expects(self::once())
            ->method('loadContent')
            ->willReturn($this->content);

        $this->productService
            ->expects(self::once())
            ->method('isProduct')
            ->with($this->content)
            ->willReturn(false);

        $this->productService
            ->expects(self::never())
            ->method('getProductFromContent');

        $this->priceService
            ->expects(self::never())
            ->method('findPricesByProductCode');

        $this->priceService
            ->expects(self::never())
            ->method('findPricesByProductCode');

        $this->priceService
            ->expects(self::never())
            ->method('deletePrice');

        $this->availabilityService
            ->expects(self::never())
            ->method('deleteProductAvailability');

        $this->subscriber->onBeforeDeleteContent($this->event);
    }

    private function getProduct(Content $content): ProductInterface
    {
        return new Product(
            $this->createMock(ProductTypeInterface::class),
            $content,
            self::PRODUCT_CODE,
        );
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\PriceInterface[] $prices
     */
    private function getPriceList(array $prices): PriceListInterface
    {
        return new PriceList($prices, 2);
    }
}
