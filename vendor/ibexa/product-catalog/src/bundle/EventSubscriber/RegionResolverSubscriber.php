<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\Events\RegionResolveEvent;
use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RegionResolverSubscriber implements EventSubscriberInterface
{
    private RegionServiceInterface $regionService;

    private ConfigResolverInterface $configResolver;

    public function __construct(RegionServiceInterface $regionService, ConfigResolverInterface $configResolver)
    {
        $this->regionService = $regionService;
        $this->configResolver = $configResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RegionResolveEvent::class => ['onRegionResolve', -100],
        ];
    }

    public function onRegionResolve(RegionResolveEvent $event): void
    {
        $identifier = $this->resolveRegionIdentifier();
        if ($identifier !== null) {
            $event->setRegion($this->regionService->getRegion($identifier));
        }
    }

    private function resolveRegionIdentifier(): ?string
    {
        $regions = $this->configResolver->getParameter('product_catalog.regions');
        if (!empty($regions)) {
            return reset($regions);
        }

        return null;
    }
}
