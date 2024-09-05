<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\Repository;

use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;
use Ibexa\Scheduler\Schedule\AbstractDateBasedStrategy;
use Ibexa\Scheduler\Schedule\DateBasedVersionStrategyInterface;
use Ibexa\Scheduler\ValueObject\CreateScheduledEntryStruct;

final class DateBasedVersionStrategy extends AbstractDateBasedStrategy implements DateBasedVersionStrategyInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo
     * @param \DateTimeInterface $when
     * @param string $action
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function scheduleVersion(VersionInfo $versionInfo, DateTimeInterface $when, string $action): ScheduledEntry
    {
        $contentInfo = $versionInfo->getContentInfo();

        $createScheduledEntryStruct = new CreateScheduledEntryStruct();
        $createScheduledEntryStruct->userId = $this->permissionResolver->getCurrentUserReference()->getUserId();
        $createScheduledEntryStruct->contentId = $contentInfo->id;
        $createScheduledEntryStruct->versionId = $versionInfo->id;
        $createScheduledEntryStruct->versionNumber = $versionInfo->versionNo;
        $createScheduledEntryStruct->actionTimestamp = $when->getTimestamp();
        $createScheduledEntryStruct->action = $action;
        $createScheduledEntryStruct->urlRoot = '';

        $this->persistenceHandler->insertVersionEntry($createScheduledEntryStruct);

        return $this->getScheduledVersion($versionInfo->id, $action);
    }

    public function isVersionScheduled(int $versionId, string $action): bool
    {
        return $this->persistenceHandler->hasVersionEntry(
            $versionId,
            $action
        );
    }

    public function countUserScheduledVersions(string $action): int
    {
        $currentUser = $this->permissionResolver->getCurrentUserReference();

        return $this->persistenceHandler->countUserVersionsEntries(
            $currentUser->getUserId(),
            $action
        );
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateScheduledEntry(ScheduledEntry $scheduledEntry, DateTimeInterface $when): ScheduledEntry
    {
        $versionInfo = $scheduledEntry->versionInfo;

        $this->updateEntry($scheduledEntry, $when);

        return $this->getScheduledVersion($versionInfo->id, $scheduledEntry->action);
    }

    public function countScheduledEntries(string $action): int
    {
        return $this->persistenceHandler->countVersionsEntries($action);
    }

    public function countScheduledEntriesToProcess(string $action): int
    {
        $now = new \DateTime();

        return $this->persistenceHandler->countVersionsEntriesOlderThan(
            (int)$now->format('U'),
            $action
        );
    }

    public function unscheduleVersion(int $versionId, string $action): bool
    {
        return $this->persistenceHandler->deleteVersionEntry($versionId, $action);
    }

    public function getScheduledVersion(int $versionId, string $action): ?ScheduledEntry
    {
        $spiScheduledEntry = $this->persistenceHandler->getVersionEntry(
            $versionId,
            $action
        );

        return $this->buildScheduledDomainObject($spiScheduledEntry);
    }

    public function getScheduledVersions(string $action, int $page = 0, int $limit = 25): iterable
    {
        return $this->buildScheduledDomainObjectList(
            $this->persistenceHandler->getVersionsEntries($action, $page, $limit)
        );
    }

    public function getScheduledEntriesToProcess(string $action, int $limit = 25): iterable
    {
        $now = new \DateTime();

        return $this->buildScheduledDomainObjectList(
            $this->persistenceHandler->getVersionsEntriesOlderThan(
                (int)$now->format('U'),
                $action,
                0,
                $limit
            )
        );
    }

    public function getUserScheduledVersions(string $action, int $page = 0, int $limit = 25): iterable
    {
        $currentUser = $this->permissionResolver->getCurrentUserReference();

        return $this->buildScheduledDomainObjectList(
            $this->persistenceHandler->getUserVersionsEntries(
                $currentUser->getUserId(),
                $action,
                $page,
                $limit
            )
        );
    }

    public function getScheduledEntriesInDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        string $action,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable {
        /** @var \Ibexa\Scheduler\ValueObject\ScheduledEntry[] $spiScheduledVersions */
        $spiScheduledVersions = $this->persistenceHandler->getVersionsEntriesByDateRange(
            (int)$start->format('U'),
            (int)$end->format('U'),
            $action,
            $this->getLanguagesIds($languages),
            $sinceId,
            $limit
        );

        return $this->buildScheduledDomainObjectList($spiScheduledVersions);
    }

    public function countScheduledEntriesInDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        string $action,
        array $languages = [],
        ?int $sinceId = null
    ): int {
        return $this->persistenceHandler->countVersionsEntriesInDateRange(
            (int)$start->format('U'),
            (int)$end->format('U'),
            $action,
            $this->getLanguagesIds($languages),
            $sinceId
        );
    }

    public function countVersionsEntriesForContent(int $contentId, string $action): int
    {
        return $this->persistenceHandler->countVersionsEntriesForContent(
            $contentId,
            $action
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionsEntriesForContent(int $contentId, string $action, int $page = 0, int $limit = 25): iterable
    {
        $spiScheduledVersions = $this->persistenceHandler->getVersionsEntriesForContent(
            $contentId,
            $action,
            $page,
            $limit
        );

        return $this->buildScheduledDomainObjectList($spiScheduledVersions);
    }
}

class_alias(DateBasedVersionStrategy::class, 'EzSystems\DateBasedPublisher\Core\Repository\DateBasedVersionStrategy');
