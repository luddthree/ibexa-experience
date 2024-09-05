<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Scheduler\Repository;

use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;

interface DateBasedPublishServiceInterface extends DateBasedEntriesListInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo
     * @param \DateTimeInterface $when
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry
     */
    public function schedulePublish(VersionInfo $versionInfo, DateTimeInterface $when): ScheduledEntry;

    public function unschedulePublish(int $versionId): bool;

    public function isScheduledPublish(int $versionId): bool;

    /**
     * @param \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry $scheduledEntry
     * @param \DateTimeInterface $when
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry
     */
    public function updateScheduledPublish(
        ScheduledEntry $scheduledEntry,
        DateTimeInterface $when
    ): ScheduledEntry;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function getScheduledPublish(int $versionId): ScheduledEntry;

    /**
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getScheduledVersions(int $page = 0, int $limit = 25): iterable;

    /**
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getUserScheduledVersions(int $page, int $limit): iterable;

    /**
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getVersionsEntriesForContent(int $contentId, int $page = 0, int $limit = 25): iterable;

    public function countUserScheduledVersions(): int;

    public function countVersionsEntriesForContent(int $contentId): int;
}

class_alias(DateBasedPublishServiceInterface::class, 'EzSystems\DateBasedPublisher\API\Repository\DateBasedPublishServiceInterface');
