<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Calendar\Provider;

use DateTimeInterface;

interface EntriesProviderInterface
{
    /**
     * @param int[] $scheduleVersionIds
     *
     * @return \Ibexa\FieldTypePage\Calendar\ScheduledEntryInterface[]
     */
    public function getScheduledEntriesByIds(array $scheduleVersionIds): iterable;

    /**
     * Return scheduled entries for given date range with optional skipping entries to the given $sinceId.
     *
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $end
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language[] $languages
     * @param int|null $sinceId
     * @param int $limit
     *
     * @return \Ibexa\FieldTypePage\Calendar\ScheduledEntryInterface[]
     */
    public function getScheduledEntriesInDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable;

    public function countScheduledEntries(): int;

    /**
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

class_alias(EntriesProviderInterface::class, 'EzSystems\EzPlatformPageFieldType\Calendar\Provider\EntriesProviderInterface');
