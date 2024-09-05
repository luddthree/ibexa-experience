<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event;

use DateTime;
use DateTimeInterface;
use Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface;

abstract class AbstractEvent implements EventInterface
{
    public const IDENTIFIER_PREFIX = 'sbe-';

    /** @var string */
    protected $id;

    /** @var \DateTime */
    protected $dateTime;

    /**
     * @param string $id
     * @param \DateTime $dateTime
     */
    public function __construct(string $id, DateTime $dateTime)
    {
        $this->id = $id;
        $this->dateTime = $dateTime;
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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function setDateTime(DateTimeInterface $dateTime): void
    {
        $this->dateTime = $dateTime;
    }
}

class_alias(AbstractEvent::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\AbstractEvent');
