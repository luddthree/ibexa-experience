<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event;

use DateTime;

class ItemsReorderedEvent extends AbstractEvent
{
    /** @var string[] */
    protected $map;

    /**
     * @param string $id
     * @param \DateTime $dateTime
     * @param string[] $map
     */
    public function __construct(string $id, DateTime $dateTime, array $map)
    {
        $this->map = $map;

        parent::__construct($id, $dateTime);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'itemsReordered';
    }

    /**
     * @return string[]
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * @param string[] $map
     */
    public function setMap(array $map): void
    {
        $this->map = $map;
    }
}

class_alias(ItemsReorderedEvent::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\ItemsReorderedEvent');
