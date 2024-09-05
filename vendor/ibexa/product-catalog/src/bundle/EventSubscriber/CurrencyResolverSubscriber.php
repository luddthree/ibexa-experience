<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Events\CurrencyResolveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CurrencyResolverSubscriber implements EventSubscriberInterface
{
    private CurrencyServiceInterface $currencyService;

    private ConfigResolverInterface $configResolver;

    public function __construct(CurrencyServiceInterface $currencyService, ConfigResolverInterface $configResolver)
    {
        $this->currencyService = $currencyService;
        $this->configResolver = $configResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CurrencyResolveEvent::class => ['onCurrencyResolve', -100],
        ];
    }

    public function onCurrencyResolve(CurrencyResolveEvent $event): void
    {
        $code = $this->resolveCurrencyCode();
        if ($code !== null) {
            $event->setCurrency($this->currencyService->getCurrencyByCode($code));
        }
    }

    private function resolveCurrencyCode(): ?string
    {
        $currencies = $this->configResolver->getParameter('product_catalog.currencies');
        if (!empty($currencies)) {
            return reset($currencies);
        }

        return null;
    }
}
