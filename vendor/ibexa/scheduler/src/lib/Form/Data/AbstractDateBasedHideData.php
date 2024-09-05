<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;

abstract class AbstractDateBasedHideData
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null */
    private $location;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo|null */
    private $versionInfo;

    public function __construct(
        ?Location $location = null,
        ?VersionInfo $versionInfo = null
    ) {
        $this->location = $location;
        $this->versionInfo = $versionInfo;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }

    public function getVersionInfo(): ?VersionInfo
    {
        return $this->versionInfo;
    }

    public function setVersionInfo(?VersionInfo $versionInfo): void
    {
        $this->versionInfo = $versionInfo;
    }
}

class_alias(AbstractDateBasedHideData::class, 'EzSystems\DateBasedPublisher\Core\Form\Data\AbstractDateBasedHideData');
