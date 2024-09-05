<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Bridge;

use Exception;
use Ibexa\Bundle\Commerce\Checkout\Entity\Basket;
use Ibexa\Bundle\Commerce\Eshop\Entities\Messages\CreateSalesOrderMessage;
use Ibexa\Bundle\Commerce\Eshop\Message\Event\MessageExceptionEvent;
use Ibexa\Bundle\Commerce\Eshop\Message\Event\TransportMessageEvents;
use Ibexa\Bundle\Commerce\LocalOrderManagement\Exception\LocalOrderRequiredException;
use Ibexa\Contracts\Commerce\Checkout\BasketRepositoryInterface;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Base on \Ibexa\Bundle\Commerce\PriceEngine\EventListener\StockListener::onLocalOrderException.
 *
 * @deprecated since 4.6, will be removed in 5.0. Use ibexa/checkout and ibexa/order-management packages instead
 */
final class ProductAvailabilitySubscriber implements EventSubscriberInterface
{
    private Repository $repository;

    private ProductServiceInterface $productService;

    private ProductAvailabilityServiceInterface $availabilityService;

    private BasketRepositoryInterface $basketRepository;

    public function __construct(
        Repository $repository,
        ProductServiceInterface $productService,
        ProductAvailabilityServiceInterface $availabilityService,
        BasketRepositoryInterface $basketRepository
    ) {
        $this->repository = $repository;
        $this->productService = $productService;
        $this->availabilityService = $availabilityService;
        $this->basketRepository = $basketRepository;
    }

    public function onMessageException(MessageExceptionEvent $event): void
    {
        $message = $event->getMessage();
        if (!($message instanceof CreateSalesOrderMessage)) {
            return;
        }

        $exception = $event->getException();
        if (!($exception instanceof LocalOrderRequiredException)) {
            return;
        }

        $basket = $this->findBasket($message);
        if ($basket !== null) {
            $this->repository->sudo(function () use ($basket): void {
                $this->processBasket($basket);
            });
        }
    }

    private function findBasket(CreateSalesOrderMessage $message): ?Basket
    {
        $request = $message->getRequestDocument();
        if (!isset($request->UUID->value)) {
            return null;
        }

        return $this->basketRepository->getBasketByColumns([
            'guid' => $request->UUID->value,
        ]);
    }

    private function processBasket(Basket $basket): void
    {
        $this->repository->beginTransaction();
        try {
            foreach ($basket->getLines() as $line) {
                $this->updateAvailability($line->getSku(), (int)$line->getQuantity());
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    private function updateAvailability(string $code, int $amount): void
    {
        $product = $this->productService->getProduct($code);

        $availability = $this->availabilityService->getAvailability($product);
        if (!$availability->isInfinite()) {
            $amount = min($amount, (int)$availability->getStock());

            $this->availabilityService->decreaseProductAvailability($product, $amount);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TransportMessageEvents::EXCEPTION_MESSAGE => 'onMessageException',
        ];
    }
}
