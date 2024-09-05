<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Events;

use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class RegionResolveEvent extends Event
{
    private ?RegionInterface $region;

    public function __construct(?RegionInterface $region = null)
    {
        $this->region = $region;
    }

    public function getRegion(): ?RegionInterface
    {
        return $this->region;
    }

    public function setRegion(?RegionInterface $region): void
    {
        $this->region = $region;
    }
}
