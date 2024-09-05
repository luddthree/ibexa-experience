<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Scheduler\Repository;

use DateTimeInterface;

interface DateBasedEntriesListInterface
{
    /**
     * @param int[] $scheduledEntriesIds
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getScheduledEntriesByIds(array $scheduledEntriesIds): iterable;

    /**
     * @param int $limit
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getScheduledEntriesToProcess(int $limit = 25): iterable;

    /**
     * Return scheduled entries for given date range with optional skipping entries to the given $sinceId.
     *
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language[] $languages
     * @param int|null $sinceId
     * @param int $limit
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getScheduledEntriesInDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable;

    public function countScheduledEntries(): int;

    public function countScheduledEntriesToProcess(): int;

    /**
     * Return number of scheduled entries for given date range with optional skipping entries to the given $sinceId.
     *
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language[] $languages
     * @param int|null $sinceId
     *
     * @return int
     */
    public function countScheduledEntriesInDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        array $languages = [],
        ?int $sinceId = null
    ): int;
}

class_alias(DateBasedEntriesListInterface::class, 'EzSystems\DateBasedPublisher\API\Repository\DateBasedEntriesListInterface');
