<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\ScheduleBlock;

use DateTime;
use DateTimeInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\ScheduleBlock\Snapshot\Snapshot;

/**
 * @internal
 */
class ScheduleSnapshotService
{
    /** @var \Ibexa\FieldTypePage\ScheduleBlock\ScheduleService */
    protected $scheduleService;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\Scheduler */
    protected $scheduler;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\EventListFilter */
    protected $eventListFilter;

    /**
     * @param \Ibexa\FieldTypePage\ScheduleBlock\ScheduleService $scheduleService
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Scheduler $scheduler
     * @param \Ibexa\FieldTypePage\ScheduleBlock\EventListFilter $eventListFilter
     */
    public function __construct(ScheduleService $scheduleService, Scheduler $scheduler, EventListFilter $eventListFilter)
    {
        $this->scheduleService = $scheduleService;
        $this->scheduler = $scheduler;
        $this->eventListFilter = $eventListFilter;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $scheduleBlock
     * @param \DateTime $startDate
     * @param int $amount
     *
     * @throws \Exception
     */
    public function createSnapshots(
        BlockValue $scheduleBlock,
        DateTime $startDate,
        int $amount
    ): void {
        $snapshots = [];
        $events = $scheduleBlock->getAttribute(ScheduleBlock::ATTRIBUTE_EVENTS)->getValue();
        $events = $this->scheduler->sortEvents($events);
        $timelineEventsAfterStartDate = $this->eventListFilter->getEventsAfterDate($events, $startDate);

        $snapshots[] = $this->createSnapshot($scheduleBlock, $startDate);

        $snapshotsToCreate = $amount - 1;
        $eventsPerChunk = count($timelineEventsAfterStartDate) / ($snapshotsToCreate);

        if ($eventsPerChunk > 1) {
            $eventsChunks = $this->getEventsChunks($timelineEventsAfterStartDate, $snapshotsToCreate);
            foreach ($eventsChunks as $eventsChunk) {
                $snapshots[] = $this->createSnapshot($scheduleBlock, end($eventsChunk)->getDateTime());
            }
        }

        $scheduleBlock->getAttribute(ScheduleBlock::ATTRIBUTE_SNAPSHOTS)->setValue($snapshots);
        $scheduleBlock->getAttribute(ScheduleBlock::ATTRIBUTE_EVENTS)->setValue($timelineEventsAfterStartDate);
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $scheduleBlock
     * @param \DateTime $dateTime
     *
     * @return \Ibexa\FieldTypePage\ScheduleBlock\Snapshot\Snapshot
     *
     * @throws \Exception
     */
    private function createSnapshot(BlockValue $scheduleBlock, DateTime $dateTime): Snapshot
    {
        $this->scheduleService->initializeScheduleData($scheduleBlock);

        // initialize the block based on the oldest snapshot if exists
        $oldestSnapshot = $this->findOldestSnapshot($scheduleBlock);
        if (null !== $oldestSnapshot) {
            $this->initializeScheduleBlockFromSnapshot($scheduleBlock, $oldestSnapshot);
        }

        $this->scheduler->scheduleToDate($scheduleBlock, $dateTime);

        $emptySlots = [];
        $slots = $scheduleBlock->getAttribute(ScheduleBlock::ATTRIBUTE_SLOTS)->getValue();
        /** @var \Ibexa\FieldTypePage\ScheduleBlock\Slot $slot */
        foreach ($slots as $slot) {
            $slotId = $slot->getId();
            $emptySlots[$slotId] = new Slot($slotId, null);
        }

        return new Snapshot(
            $dateTime,
            $scheduleBlock->getAttribute(ScheduleBlock::ATTRIBUTE_LIMIT)->getValue(),
            $scheduleBlock->getAttribute(ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS)->getValue(),
            $emptySlots
        );
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface[] $events
     * @param int $amount
     *
     * @return array
     */
    private function getEventsChunks(array $events, int $amount): array
    {
        $items = \count($events);
        $chunkSize = ceil($items / $amount);
        $chunks = array_fill(0, $amount, []);
        $currentChunk = 0;

        foreach ($events as $event) {
            $previousChunk = max(0, $currentChunk - 1);
            $targetChunk = !empty($chunks[$previousChunk]) && end($chunks[$previousChunk])->getDateTime() == $event->getDateTime()
                ? $previousChunk
                : $currentChunk;

            $chunks[$targetChunk][] = $event;
            $currentChunk = \count($chunks[$currentChunk]) >= $chunkSize
                ? $targetChunk + 1
                : $currentChunk;
        }

        return array_filter($chunks);
    }

    /**
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Snapshot\Snapshot $snapshot
     *
     * @return array
     */
    public function initializeAttributesFromSnapshot(Snapshot $snapshot): array
    {
        return [
            ScheduleBlock::ATTRIBUTE_SLOTS => $snapshot->getSlots(),
            ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS => $snapshot->getInitialItems(),
            ScheduleBlock::ATTRIBUTE_LIMIT => $snapshot->getLimit(),
            ScheduleBlock::ATTRIBUTE_LOADED_SNAPSHOT => $snapshot,
        ];
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $scheduleBlock
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Snapshot\Snapshot $snapshot
     */
    public function initializeScheduleBlockFromSnapshot(BlockValue $scheduleBlock, Snapshot $snapshot): void
    {
        foreach ($this->initializeAttributesFromSnapshot($snapshot) as $identifier => $value) {
            $scheduleBlock->getAttribute($identifier)->setValue($value);
        }
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $scheduleBlock
     * @param \DateTimeInterface $dateTime
     *
     * @return \Ibexa\FieldTypePage\ScheduleBlock\Snapshot\Snapshot|null
     */
    public function findMostSuitableSnapshot(
        BlockValue $scheduleBlock,
        DateTimeInterface $dateTime
    ): ?Snapshot {
        $snapshots = array_filter(
            $scheduleBlock->getAttribute(ScheduleBlock::ATTRIBUTE_SNAPSHOTS)->getValue(),
            static function (Snapshot $snapshot) use ($dateTime): bool {
                return $snapshot->getDate() < $dateTime;
            }
        );

        uasort(
            $snapshots,
            static function (Snapshot $a, Snapshot $b) {
                return $b->getDate()->getTimestamp() - $a->getDate()->getTimestamp();
            }
        );

        return array_shift($snapshots);
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \DateTimeInterface $dateTime
     */
    public function restoreFromSnapshot(BlockValue $blockValue, DateTimeInterface $dateTime): void
    {
        $snapshot = null !== $dateTime
            ? $this->findMostSuitableSnapshot($blockValue, $dateTime)
            : null;

        if (null === $snapshot) {
            return;
        }

        $this->initializeScheduleBlockFromSnapshot($blockValue, $snapshot);
        $items = $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS)->getValue();
        $this->scheduleService->arrangeItems($blockValue, $items);
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $scheduleBlock
     *
     * @return \Ibexa\FieldTypePage\ScheduleBlock\Snapshot\Snapshot|null
     */
    private function findOldestSnapshot(BlockValue $scheduleBlock): ?Snapshot
    {
        $oldestSnapshot = null;

        /** @var \Ibexa\FieldTypePage\ScheduleBlock\Snapshot\Snapshot $snapshot */
        foreach ($scheduleBlock->getAttribute(ScheduleBlock::ATTRIBUTE_SNAPSHOTS)->getValue() as $snapshot) {
            if (null === $oldestSnapshot || $snapshot->getDate() < $oldestSnapshot->getDate()) {
                $oldestSnapshot = $snapshot;
            }
        }

        return $oldestSnapshot;
    }
}

class_alias(ScheduleSnapshotService::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\ScheduleSnapshotService');
