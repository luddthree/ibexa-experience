<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event;

use DateTime;

class ItemRemovedEvent extends AbstractEvent
{
    /** @var string */
    protected $itemId;

    /**
     * @param string $id
     * @param \DateTime $dateTime
     * @param string $itemId
     */
    public function __construct(string $id, DateTime $dateTime, string $itemId)
    {
        $this->itemId = $itemId;

        parent::__construct($id, $dateTime);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'itemRemoved';
    }

    /**
     * @return string
     */
    public function getItemId(): string
    {
        return $this->itemId;
    }

    /**
     * @param string $itemId
     */
    public function setItemId($itemId): void
    {
        $this->itemId = $itemId;
    }
}

class_alias(ItemRemovedEvent::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\ItemRemovedEvent');
