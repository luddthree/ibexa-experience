<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Event;

use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeCreateProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDecreaseProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDeleteProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeIncreaseProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeUpdateProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\CreateProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DecreaseProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DeleteProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\IncreaseProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceDecorator;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ProductAvailabilityService extends ProductAvailabilityServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ProductAvailabilityServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createProductAvailability(ProductAvailabilityCreateStruct $struct): AvailabilityInterface
    {
        $beforeEvent = new BeforeCreateProductAvailabilityEvent($struct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultProductAvailability();
        }

        $productAvailability = $beforeEvent->hasResultProductAvailability()
            ? $beforeEvent->getResultProductAvailability()
            : $this->innerService->createProductAvailability($struct);

        $this->eventDispatcher->dispatch(new CreateProductAvailabilityEvent($struct, $productAvailability));

        return $productAvailability;
    }

    public function updateProductAvailability(ProductAvailabilityUpdateStruct $struct): AvailabilityInterface
    {
        $beforeEvent = new BeforeUpdateProductAvailabilityEvent($struct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultProductAvailability();
        }

        $productAvailability = $beforeEvent->hasResultProductAvailability()
            ? $beforeEvent->getResultProductAvailability()
            : $this->innerService->updateProductAvailability($struct);

        $this->eventDispatcher->dispatch(new UpdateProductAvailabilityEvent(
            $productAvailability,
            $struct
        ));

        return $productAvailability;
    }

    public function increaseProductAvailability(ProductInterface $product, int $amount = 1): AvailabilityInterface
    {
        $beforeEvent = new BeforeIncreaseProductAvailabilityEvent($product, $amount);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultProductAvailability();
        }

        $productAvailability = $beforeEvent->hasResultProductAvailability()
            ? $beforeEvent->getResultProductAvailability()
            : $this->innerService->increaseProductAvailability($product, $amount);

        $this->eventDispatcher->dispatch(new IncreaseProductAvailabilityEvent(
            $productAvailability,
            $product,
            $amount
        ));

        return $productAvailability;
    }

    public function decreaseProductAvailability(ProductInterface $product, int $amount = 1): AvailabilityInterface
    {
        $beforeEvent = new BeforeDecreaseProductAvailabilityEvent($product, $amount);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultProductAvailability();
        }

        $productAvailability = $beforeEvent->hasResultProductAvailability()
            ? $beforeEvent->getResultProductAvailability()
            : $this->innerService->decreaseProductAvailability($product, $amount);

        $this->eventDispatcher->dispatch(new DecreaseProductAvailabilityEvent(
            $productAvailability,
            $product,
            $amount
        ));

        return $productAvailability;
    }

    public function deleteProductAvailability(
        ProductInterface $product
    ): void {
        $beforeEvent = new BeforeDeleteProductAvailabilityEvent($product);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteProductAvailability($product);

        $this->eventDispatcher->dispatch(new DeleteProductAvailabilityEvent($product));
    }
}
