<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\CurrencyResolverInterface;
use Ibexa\Contracts\ProductCatalog\Events\CurrencyResolveEvent;
use Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CurrencyResolver implements CurrencyResolverInterface
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws \Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException
     */
    public function resolveCurrency(): CurrencyInterface
    {
        $event = $this->eventDispatcher->dispatch(new CurrencyResolveEvent());

        $currency = $event->getCurrency();
        if ($currency === null) {
            throw new ConfigurationException(
                'Cannot resolve currency. Missing configuration under the ibexa.system.[your_siteaccess].product_catalog.currencies key'
            );
        }

        return $currency;
    }
}
