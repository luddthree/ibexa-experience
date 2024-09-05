<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Schedule;

use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;

interface DateBasedVersionStrategyInterface extends DateBasedEntriesListInterface
{
    public function isVersionScheduled(int $versionId, string $action): bool;

    /**
     * @param int $versionId
     * @param string $action
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry|null
     */
    public function getScheduledVersion(int $versionId, string $action): ?ScheduledEntry;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo
     * @param \DateTimeInterface $when
     * @param string $action
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry
     */
    public function scheduleVersion(VersionInfo $versionInfo, DateTimeInterface $when, string $action): ScheduledEntry;

    public function unscheduleVersion(int $versionId, string $action): bool;

    /**
     * @param \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry $scheduledEntry
     * @param \DateTimeInterface $when
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry
     */
    public function updateScheduledEntry(ScheduledEntry $scheduledEntry, DateTimeInterface $when): ScheduledEntry;

    /**
     * @param string $action
     * @param int $page
     * @param int $limit
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getScheduledVersions(string $action, int $page = 0, int $limit = 25): iterable;

    /**
     * @param string $action
     * @param int $page
     * @param int $limit
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getUserScheduledVersions(string $action, int $page = 0, int $limit = 25): iterable;

    /**
     * @param int $contentId
     * @param string $action
     * @param int $page
     * @param int $limit
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getVersionsEntriesForContent(int $contentId, string $action, int $page = 0, int $limit = 25): iterable;

    public function countUserScheduledVersions(string $action): int;

    public function countVersionsEntriesForContent(int $contentId, string $action): int;
}

class_alias(DateBasedVersionStrategyInterface::class, 'EzSystems\DateBasedPublisher\SPI\Schedule\DateBasedVersionStrategyInterface');
