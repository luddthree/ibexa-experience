<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event;

use DateTime;

class LimitChangedEvent extends AbstractEvent
{
    /** @var int */
    protected $limit;

    /**
     * @param string $id
     * @param \DateTime $dateTime
     * @param int $limit
     */
    public function __construct(string $id, DateTime $dateTime, int $limit)
    {
        $this->limit = $limit;

        parent::__construct($id, $dateTime);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'limitChanged';
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

class_alias(LimitChangedEvent::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\LimitChangedEvent');
