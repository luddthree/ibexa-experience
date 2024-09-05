<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Bridge;

use Exception;
use Ibexa\Bundle\Commerce\Checkout\Entity\Basket;
use Ibexa\Bundle\Commerce\Checkout\Entity\BasketLine;
use Ibexa\Bundle\Commerce\Eshop\Entities\Messages\CreateSalesOrderMessage;
use Ibexa\Bundle\Commerce\Eshop\Message\AbstractMessage;
use Ibexa\Bundle\Commerce\Eshop\Message\Event\MessageExceptionEvent;
use Ibexa\Bundle\Commerce\LocalOrderManagement\Exception\LocalOrderRequiredException;
use Ibexa\Contracts\Commerce\Checkout\BasketRepositoryInterface;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Bridge\ProductAvailabilitySubscriber;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ProductAvailabilitySubscriberTest extends TestCase
{
    private const EXAMPLE_GUID = 'c127a430-9b00-4beb-9fc2-e38ff99fcad1';
    private const EXAMPLE_PRODUCT_CODE = 'foo';
    private const EXAMPLE_QUANTITY = 10;

    /** @var \Ibexa\Contracts\Core\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject */
    private Repository $repository;

    /** @var \Ibexa\Contracts\ProductCatalog\ProductServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductServiceInterface $productService;

    /** @var \Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductAvailabilityServiceInterface $availabilityService;

    /** @var \Ibexa\Contracts\Commerce\Checkout\BasketRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private BasketRepositoryInterface $basketRepository;

    private ProductAvailabilitySubscriber $subscriber;

    protected function setUp(): void
    {
        $this->repository = $this->createRepositoryMock();
        $this->productService = $this->createMock(ProductServiceInterface::class);
        $this->availabilityService = $this->createMock(ProductAvailabilityServiceInterface::class);
        $this->basketRepository = $this->createMock(BasketRepositoryInterface::class);

        $this->subscriber = new ProductAvailabilitySubscriber(
            $this->repository,
            $this->productService,
            $this->availabilityService,
            $this->basketRepository
        );
    }

    /**
     * @dataProvider dataProviderForTestSubscriberSkipEvent
     */
    public function testSubscriberSkipEvent(MessageExceptionEvent $event): void
    {
        $this->basketRepository
            ->expects(self::never())
            ->method('getBasketByColumns');

        $this->subscriber->onMessageException($event);
    }

    /**
     * @return iterable<string, array<\Ibexa\Bundle\Commerce\Eshop\Message\Event\MessageExceptionEvent>>
     */
    public function dataProviderForTestSubscriberSkipEvent(): iterable
    {
        yield 'non supported message' => [
            $this->createMessageExceptionEvent(
                $this->createMock(AbstractMessage::class),
                $this->createMock(LocalOrderRequiredException::class)
            ),
        ];

        yield 'non supported exception' => [
            $this->createMessageExceptionEvent(
                $this->createMock(CreateSalesOrderMessage::class),
                $this->createMock(Exception::class)
            ),
        ];
    }

    public function testSubscriberDecreaseProductStock(): void
    {
        $message = new CreateSalesOrderMessage();
        $message->setRequestDocument($this->createRequestDocumentWithGUID(self::EXAMPLE_GUID));

        $event = new MessageExceptionEvent();
        $event->setMessage($message);
        $event->setException($this->createMock(LocalOrderRequiredException::class));

        $this->configureBasketRepository(
            self::EXAMPLE_GUID,
            $this->createBasket([
                self::EXAMPLE_PRODUCT_CODE => self::EXAMPLE_QUANTITY,
            ])
        );

        $this->assertStockIsDecreased(self::EXAMPLE_PRODUCT_CODE, self::EXAMPLE_QUANTITY);

        $this->subscriber->onMessageException($event);
    }

    public function testSubscriberSkipProductsWithInfiniteProductStock(): void
    {
        $message = new CreateSalesOrderMessage();
        $message->setRequestDocument($this->createRequestDocumentWithGUID(self::EXAMPLE_GUID));

        $event = new MessageExceptionEvent();
        $event->setMessage($message);
        $event->setException($this->createMock(LocalOrderRequiredException::class));

        $this->configureBasketRepository(
            self::EXAMPLE_GUID,
            $this->createBasket([
                self::EXAMPLE_PRODUCT_CODE => self::EXAMPLE_QUANTITY,
            ])
        );

        $this->assertInfiniteStockIsNotDecreased(self::EXAMPLE_PRODUCT_CODE, self::EXAMPLE_QUANTITY);

        $this->subscriber->onMessageException($event);
    }

    private function assertStockIsDecreased(string $code, int $amount): void
    {
        $product = $this->createMock(ProductInterface::class);

        $this->productService
            ->method('getProduct')
            ->with($code, null)
            ->willReturn($product);

        $this->availabilityService
            ->method('getAvailability')
            ->with($product, null)
            ->willReturn($this->createAvailability(false, PHP_INT_MAX));

        $this->availabilityService
            ->expects(self::once())
            ->method('decreaseProductAvailability')
            ->with($product, $amount);
    }

    private function assertInfiniteStockIsNotDecreased(string $code, int $amount): void
    {
        $product = $this->createMock(ProductInterface::class);

        $this->productService
            ->method('getProduct')
            ->with($code, null)
            ->willReturn($product);

        $this->availabilityService
            ->method('getAvailability')
            ->with($product, null)
            ->willReturn($this->createAvailability(true, 0));

        $this->availabilityService
            ->expects(self::never())
            ->method('decreaseProductAvailability')
            ->with($product, $amount);
    }

    private function configureBasketRepository(string $guid, Basket $basket): void
    {
        $this->basketRepository
            ->method('getBasketByColumns')
            ->with(['guid' => $guid])
            ->willReturn($basket);
    }

    private function createAvailability(bool $isInfinite, int $stock): AvailabilityInterface
    {
        $availability = $this->createMock(AvailabilityInterface::class);
        $availability->method('isInfinite')->willReturn($isInfinite);
        $availability->method('getStock')->willReturn($stock);

        return $availability;
    }

    /**
     * @param array<string,int> $products
     */
    private function createBasket(array $products): Basket
    {
        $basket = new Basket();
        foreach ($products as $code => $quantity) {
            $line = new BasketLine();
            $line->setSku($code);
            $line->setQuantity((float)$quantity);

            $basket->addLine($line);
        }

        return $basket;
    }

    private function createMessageExceptionEvent(AbstractMessage $message, Exception $exception): MessageExceptionEvent
    {
        $event = new MessageExceptionEvent();
        $event->setMessage($message);
        $event->setException($exception);

        return $event;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createRepositoryMock(): Repository
    {
        $callback = fn (callable $callable) => $callable($this->repository);

        $repository = $this->createMock(Repository::class);
        $repository->method('sudo')->willReturnCallback($callback);

        return $repository;
    }

    private function createRequestDocumentWithGUID(string $guid): stdClass
    {
        $requestDocument = new stdClass();
        $requestDocument->UUID = new stdClass();
        $requestDocument->UUID->value = $guid;

        return $requestDocument;
    }
}
