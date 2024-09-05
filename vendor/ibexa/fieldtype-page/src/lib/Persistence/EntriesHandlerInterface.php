<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Persistence;

interface EntriesHandlerInterface
{
    /**
     * @param int[] $scheduleEntriesIds
     *
     * @return \Ibexa\Contracts\Core\Persistence\ValueObject[]
     */
    public function getEntriesByIds(array $scheduleEntriesIds): iterable;

    /**
     * @param int[] $languages
     *
     * @return \Ibexa\Contracts\Core\Persistence\ValueObject[]
     */
    public function getVersionsEntriesByDateRange(
        int $start,
        int $end,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable;

    public function countVersionsEntries(): int;

    public function countVersionsEntriesInDateRange(
        int $start,
        int $end,
        array $languages = [],
        ?int $sinceId = null
    ): int;
}

class_alias(EntriesHandlerInterface::class, 'EzSystems\EzPlatformPageFieldType\Persistence\EntriesHandlerInterface');
