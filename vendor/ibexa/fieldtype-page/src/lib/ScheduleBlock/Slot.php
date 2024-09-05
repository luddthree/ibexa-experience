<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\ScheduleBlock;

use Ibexa\FieldTypePage\ScheduleBlock\Item\Item;

class Slot
{
    public const IDENTIFIER_PREFIX = 'sbs-';

    /** @var string */
    protected $id;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\Item\Item */
    protected $item;

    /**
     * @param string $id
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Item\Item $item
     */
    public function __construct($id, Item $item = null)
    {
        $this->id = $id;
        $this->item = $item;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return \Ibexa\FieldTypePage\ScheduleBlock\Item\Item|null
     */
    public function getItem(): ?Item
    {
        return $this->item;
    }

    /**
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Item\Item|null $item
     */
    public function setItem(?Item $item): void
    {
        $this->item = $item;
    }
}

class_alias(Slot::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Slot');
