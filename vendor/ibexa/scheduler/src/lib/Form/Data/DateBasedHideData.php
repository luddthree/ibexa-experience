<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;

final class DateBasedHideData extends AbstractDateBasedHideData
{
    /** @var int|null */
    private $timestamp;

    public function __construct(
        ?Location $location = null,
        ?VersionInfo $versionInfo = null,
        ?int $timestamp = null
    ) {
        parent::__construct($location, $versionInfo);

        $this->timestamp = $timestamp;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(?int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }
}

class_alias(DateBasedHideData::class, 'EzSystems\DateBasedPublisher\Core\Form\Data\DateBasedHideData');
