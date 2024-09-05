<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Events\RegionResolveEvent;
use Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException;
use Ibexa\Contracts\ProductCatalog\RegionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class RegionResolver implements RegionResolverInterface
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function resolveRegion(): RegionInterface
    {
        $event = $this->eventDispatcher->dispatch(new RegionResolveEvent());

        $region = $event->getRegion();
        if ($region === null) {
            throw new ConfigurationException('Unable to resolve region');
        }

        return $region;
    }
}
