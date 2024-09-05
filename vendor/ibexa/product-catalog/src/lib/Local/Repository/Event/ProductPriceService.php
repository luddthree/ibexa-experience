<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Event;

use Ibexa\Contracts\ProductCatalog\Events\BeforeCreatePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\BeforeDeletePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\BeforeExecutePriceStructsEvent;
use Ibexa\Contracts\ProductCatalog\Events\BeforeUpdatePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\CreatePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\DeletePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\ExecutePriceStructsEvent;
use Ibexa\Contracts\ProductCatalog\Events\UpdatePriceEvent;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceDecorator;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ProductPriceService extends ProductPriceServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ProductPriceServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute(iterable $structs): void
    {
        $beforeEvent = new BeforeExecutePriceStructsEvent($structs);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->execute($structs);

        $this->eventDispatcher->dispatch(new ExecutePriceStructsEvent($structs));
    }

    public function deletePrice(ProductPriceDeleteStructInterface $struct): void
    {
        $beforeEvent = new BeforeDeletePriceEvent($struct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deletePrice($struct);

        $this->eventDispatcher->dispatch(new DeletePriceEvent($struct));
    }

    public function createProductPrice(ProductPriceCreateStructInterface $struct): PriceInterface
    {
        $beforeEvent = new BeforeCreatePriceEvent($struct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultPrice();
        }

        $price = $beforeEvent->hasResultPrice()
            ? $beforeEvent->getResultPrice()
            : $this->innerService->createProductPrice($struct);

        $this->eventDispatcher->dispatch(new CreatePriceEvent($struct, $price));

        return $price;
    }

    public function updateProductPrice(ProductPriceUpdateStructInterface $struct): PriceInterface
    {
        $beforeEvent = new BeforeUpdatePriceEvent($struct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultPrice();
        }

        $price = $beforeEvent->hasResultPrice()
            ? $beforeEvent->getResultPrice()
            : $this->innerService->updateProductPrice($struct);

        $this->eventDispatcher->dispatch(new UpdatePriceEvent($price, $struct));

        return $price;
    }
}
