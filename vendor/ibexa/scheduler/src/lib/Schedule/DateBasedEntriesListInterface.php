<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Schedule;

use DateTimeInterface;

interface DateBasedEntriesListInterface
{
    /**
     * @param int[] $scheduleVersionIds
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getScheduledEntriesByIds(array $scheduleVersionIds): iterable;

    /**
     * @param string $action
     * @param int $limit
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getScheduledEntriesToProcess(string $action, int $limit = 25): iterable;

    /**
     * Return scheduled entries for given date range with optional skipping entries to the given $sinceId.
     *
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @param string $action
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language[] $languages
     * @param int|null $sinceId
     * @param int $limit
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getScheduledEntriesInDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        string $action,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable;

    public function countScheduledEntries(string $action): int;

    public function countScheduledEntriesToProcess(string $action): int;

    /**
     * Return number of scheduled entries for given date range with optional skipping entries to the given $sinceId.
     *
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @param string $action
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language[] $languages
     * @param int|null $sinceId
     *
     * @return int
     */
    public function countScheduledEntriesInDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        string $action,
        array $languages = [],
        ?int $sinceId = null
    ): int;
}

class_alias(DateBasedEntriesListInterface::class, 'EzSystems\DateBasedPublisher\SPI\Schedule\DateBasedEntriesListInterface');
