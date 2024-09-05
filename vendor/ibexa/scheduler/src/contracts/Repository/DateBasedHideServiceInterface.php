<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Scheduler\Repository;

use DateTimeInterface;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;

interface DateBasedHideServiceInterface extends DateBasedEntriesListInterface
{
    /**
     * @param int $contentId
     * @param \DateTimeInterface $when
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry
     */
    public function scheduleHide(int $contentId, DateTimeInterface $when): ScheduledEntry;

    public function unscheduleHide(int $contentId): bool;

    public function isScheduledHide(int $contentId): bool;

    /**
     * @param \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry $scheduledEntry
     * @param \DateTimeInterface $when
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry
     */
    public function updateScheduledHide(
        ScheduledEntry $scheduledEntry,
        DateTimeInterface $when
    ): ScheduledEntry;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getScheduledHide(int $contentId): ScheduledEntry;
}

class_alias(DateBasedHideServiceInterface::class, 'EzSystems\DateBasedPublisher\API\Repository\DateBasedHideServiceInterface');
