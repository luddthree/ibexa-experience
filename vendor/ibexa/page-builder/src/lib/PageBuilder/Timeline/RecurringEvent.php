<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder\Timeline;

use Ibexa\Contracts\PageBuilder\PageBuilder\Timeline\BaseEvent;

class RecurringEvent extends BaseEvent
{
    /** @var \DateTimeInterface */
    protected $endDate;

    /** @var string */
    protected $rrule;

    public function __construct(
        string $name,
        string $description,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        string $rrule,
        string $icon
    ) {
        $this->endDate = $endDate;
        $this->rrule = $rrule;
        parent::__construct($name, $description, $startDate, $icon);
    }

    /**
     * @return \DateTimeInterface
     */
    public function getStartDate(): \DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface $startDate
     */
    public function setStartDate(\DateTimeInterface $startDate): void
    {
        $this->date = $startDate;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    /**
     * @param \DateTimeInterface $endDate
     */
    public function setEndDate(\DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return string
     */
    public function getRrule(): string
    {
        return $this->rrule;
    }

    /**
     * @param string $rrule
     */
    public function setRrule(string $rrule): void
    {
        $this->rrule = $rrule;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'recurring';
    }
}

class_alias(RecurringEvent::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\RecurringEvent');
