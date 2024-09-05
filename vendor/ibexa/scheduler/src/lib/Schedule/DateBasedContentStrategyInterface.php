<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Schedule;

use DateTimeInterface;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;

interface DateBasedContentStrategyInterface extends DateBasedEntriesListInterface
{
    public function isContentScheduled(int $contentId, string $action): bool;

    /**
     * @param int $contentId
     * @param string $action
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry|null
     */
    public function getScheduledContent(int $contentId, string $action): ?ScheduledEntry;

    /**
     * @param int $contentId
     * @param \DateTimeInterface $when
     * @param string $action
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry
     */
    public function scheduleContent(int $contentId, DateTimeInterface $when, string $action): ScheduledEntry;

    public function unscheduleContent(int $contentId, string $action): bool;

    /**
     * @param \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry $scheduledEntry
     * @param \DateTimeInterface $when
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry
     */
    public function updateScheduledEntry(ScheduledEntry $scheduledEntry, DateTimeInterface $when): ScheduledEntry;
}

class_alias(DateBasedContentStrategyInterface::class, 'EzSystems\DateBasedPublisher\SPI\Schedule\DateBasedContentStrategyInterface');
