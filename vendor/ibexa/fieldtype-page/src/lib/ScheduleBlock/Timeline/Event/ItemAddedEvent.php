<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event;

use DateTime;
use Ibexa\FieldTypePage\ScheduleBlock\Item\Item;

class ItemAddedEvent extends AbstractEvent
{
    /** @var \Ibexa\FieldTypePage\ScheduleBlock\Item\Item */
    protected $item;

    /**
     * @param string $id
     * @param \DateTime $dateTime
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Item\Item $item
     */
    public function __construct(string $id, DateTime $dateTime, Item $item)
    {
        $this->item = $item;

        parent::__construct($id, $dateTime);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'itemAdded';
    }

    /**
     * @return \Ibexa\FieldTypePage\ScheduleBlock\Item\Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Item\Item $item
     */
    public function setItem(Item $item): void
    {
        $this->item = $item;
    }
}

class_alias(ItemAddedEvent::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\ItemAddedEvent');
