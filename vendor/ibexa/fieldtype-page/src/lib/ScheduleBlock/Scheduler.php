<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\ScheduleBlock;

use DateTimeInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface;
use Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event\Processor\EventProcessorDispatcher;

class Scheduler
{
    /** @var \Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event\Processor\EventProcessorDispatcher */
    private $eventProcessorDispatcher;

    /**
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event\Processor\EventProcessorDispatcher $eventProcessor
     */
    public function __construct(EventProcessorDispatcher $eventProcessor)
    {
        $this->eventProcessorDispatcher = $eventProcessor;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \DateTimeInterface $date
     */
    public function scheduleToDate(BlockValue $blockValue, DateTimeInterface $date = null): void
    {
        /** @var \Ibexa\FieldTypePage\ScheduleBlock\Snapshot\Snapshot|null $loadedSnapshot */
        $loadedSnapshot = $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_LOADED_SNAPSHOT)->getValue();

        $events = $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_EVENTS)->getValue();
        $events = $this->filterEvents($events, null !== $loadedSnapshot ? $loadedSnapshot->getDate() : null, $date);
        $events = $this->sortEvents($events);

        foreach ($events as $event) {
            if (null !== $loadedSnapshot && $loadedSnapshot->getDate() >= $event->getDateTime()) {
                continue;
            }

            $this->eventProcessorDispatcher->dispatch($event, $blockValue);
        }
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface[] $events
     * @param \DateTimeInterface|null $in
     * @param \DateTimeInterface|null $out
     *
     * @return \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface[]
     */
    public function filterEvents(array $events, DateTimeInterface $in = null, DateTimeInterface $out = null): array
    {
        $filteredEvents = [];
        foreach ($events as $event) {
            if (
                (null === $in && $event->getDateTime() <= $out)
                || (null === $out && $event->getDateTime() >= $in)
                || (null !== $in && $event->getDateTime() >= $in && null !== $out && $event->getDateTime() <= $out)
            ) {
                $filteredEvents[] = $event;
            }
        }

        return $filteredEvents;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface[] $events
     *
     * @return \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface[]
     */
    public function sortEvents(array $events): array
    {
        $additionOrder = [];
        $orderIndex = 0;
        foreach ($events as $event) {
            $additionOrder[$event->getId()] = $orderIndex++;
        }

        usort(
            $events,
            static function (EventInterface $a, EventInterface $b) use ($additionOrder) {
                $aTimestamp = $a->getDateTime()->getTimestamp();
                $bTimestamp = $b->getDateTime()->getTimestamp();

                if ($aTimestamp == $bTimestamp) {
                    return $additionOrder[$a->getId()] <=> $additionOrder[$b->getId()];
                }

                return $aTimestamp <=> $bTimestamp;
            }
        );

        return $events;
    }
}

class_alias(Scheduler::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Scheduler');
