<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\ScheduleBlock\Item;

use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

class Item
{
    public const IDENTIFIER_PREFIX = 'sbi-';

    /** @var string */
    protected $id;

    /** @var \DateTimeInterface */
    protected $additionDate;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    protected $location;

    /**
     * @param string $id
     * @param \DateTimeInterface $additionDate
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     */
    public function __construct(
        string $id,
        DateTimeInterface $additionDate,
        Location $location
    ) {
        $this->id = $id;
        $this->additionDate = $additionDate;
        $this->location = $location;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getAdditionDate(): DateTimeInterface
    {
        return $this->additionDate;
    }

    public function setAdditionDate(DateTimeInterface $dateTime): void
    {
        $this->additionDate = $dateTime;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     */
    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }
}

class_alias(Item::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Item\Item');
