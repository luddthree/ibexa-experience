<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Event;

use Ibexa\Contracts\ProductCatalog\CurrencyServiceDecorator;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Events\BeforeCreateCurrencyEvent;
use Ibexa\Contracts\ProductCatalog\Events\BeforeDeleteCurrencyEvent;
use Ibexa\Contracts\ProductCatalog\Events\BeforeUpdateCurrencyEvent;
use Ibexa\Contracts\ProductCatalog\Events\CreateCurrencyEvent;
use Ibexa\Contracts\ProductCatalog\Events\DeleteCurrencyEvent;
use Ibexa\Contracts\ProductCatalog\Events\UpdateCurrencyEvent;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CurrencyService extends CurrencyServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(CurrencyServiceInterface $innerService, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createCurrency(CurrencyCreateStruct $struct): CurrencyInterface
    {
        $beforeEvent = new BeforeCreateCurrencyEvent($struct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultCurrency();
        }

        $currency = $beforeEvent->hasResultCurrency()
            ? $beforeEvent->getResultCurrency()
            : $this->innerService->createCurrency($struct);

        $this->eventDispatcher->dispatch(new CreateCurrencyEvent($struct, $currency));

        return $currency;
    }

    public function deleteCurrency(CurrencyInterface $currency): void
    {
        $beforeEvent = new BeforeDeleteCurrencyEvent($currency);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteCurrency($currency);

        $this->eventDispatcher->dispatch(new DeleteCurrencyEvent($currency));
    }

    public function updateCurrency(CurrencyInterface $currency, CurrencyUpdateStruct $struct): CurrencyInterface
    {
        $beforeEvent = new BeforeUpdateCurrencyEvent($currency, $struct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultCurrency();
        }

        $currency = $beforeEvent->hasResultCurrency()
            ? $beforeEvent->getResultCurrency()
            : $this->innerService->updateCurrency($currency, $struct);

        $this->eventDispatcher->dispatch(new UpdateCurrencyEvent($currency, $struct));

        return $currency;
    }
}
