<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Event;

use Ibexa\Contracts\ProductCatalog\CatalogServiceDecorator;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeCreateCatalogEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDeleteCatalogEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeUpdateCatalogEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\CreateCatalogEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DeleteCatalogEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateCatalogEvent;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CatalogService extends CatalogServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        CatalogServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createCatalog(CatalogCreateStruct $createStruct): CatalogInterface
    {
        $beforeEvent = new BeforeCreateCatalogEvent($createStruct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultCatalog();
        }

        $catalog = $beforeEvent->hasResultCatalog()
            ? $beforeEvent->getResultCatalog()
            : $this->innerService->createCatalog($createStruct);

        $this->eventDispatcher->dispatch(new CreateCatalogEvent($createStruct, $catalog));

        return $catalog;
    }

    public function updateCatalog(
        CatalogInterface $catalog,
        CatalogUpdateStruct $updateStruct
    ): CatalogInterface {
        $beforeEvent = new BeforeUpdateCatalogEvent(
            $catalog,
            $updateStruct
        );

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultCatalog();
        }

        $catalog = $beforeEvent->hasResultCatalog()
            ? $beforeEvent->getResultCatalog()
            : $this->innerService->updateCatalog($catalog, $updateStruct);

        $this->eventDispatcher->dispatch(new UpdateCatalogEvent($catalog, $updateStruct));

        return $catalog;
    }

    public function deleteCatalog(CatalogInterface $catalog): void
    {
        $beforeEvent = new BeforeDeleteCatalogEvent($catalog);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteCatalog($catalog);

        $this->eventDispatcher->dispatch(new DeleteCatalogEvent($catalog));
    }
}
