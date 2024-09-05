<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\ScheduleBlock\Snapshot;

use DateTime;

/**
 * @internal
 */
class Snapshot
{
    /** @var \DateTime */
    private $date;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\Item\Item[] */
    private $initialItems;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\Slot[] */
    private $slots;

    /** @var int */
    private $limit;

    /**
     * @param \DateTime $date
     * @param int $limit
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Item\Item[] $initialItems
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Slot[] $slots
     */
    public function __construct(
        DateTime $date,
        int $limit,
        array $initialItems = [],
        array $slots = []
    ) {
        $this->date = $date;
        $this->limit = $limit;
        $this->initialItems = $initialItems;
        $this->slots = $slots;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return \Ibexa\FieldTypePage\ScheduleBlock\Item\Item[]
     */
    public function getInitialItems(): array
    {
        return $this->initialItems;
    }

    /**
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Item\Item[] $initialItems
     */
    public function setInitialItems(array $initialItems): void
    {
        $this->initialItems = $initialItems;
    }

    /**
     * @return \Ibexa\FieldTypePage\ScheduleBlock\Slot[]
     */
    public function getSlots(): array
    {
        return $this->slots;
    }

    /**
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Slot[] $slots
     */
    public function setSlots(array $slots): void
    {
        $this->slots = $slots;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }
}

class_alias(Snapshot::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Snapshot\Snapshot');
