<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Repository;

use DateTimeInterface;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Scheduler\Schedule\AbstractDateBasedStrategy;
use Ibexa\Scheduler\Schedule\DateBasedContentStrategyInterface;
use Ibexa\Scheduler\ValueObject\CreateScheduledEntryStruct;

final class DateBasedContentStrategy extends AbstractDateBasedStrategy implements DateBasedContentStrategyInterface
{
    public function isContentScheduled(int $contentId, string $action): bool
    {
        return $this->persistenceHandler->hasContentEntry(
            $contentId,
            $action
        );
    }

    /**
     * @param int $contentId
     * @param \DateTimeInterface $when
     * @param string $action
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function scheduleContent(int $contentId, DateTimeInterface $when, string $action): ScheduledEntry
    {
        $content = $this->contentService->loadContent($contentId);

        if (true === $content->contentInfo->isHidden) {
            throw new InvalidArgumentException('$contentInfo->isHidden', 'Cannot schedule hide currently hidden content!');
        }

        if ($this->isContentScheduled($contentId, $action)) {
            return $this->updateScheduledEntry(
                $this->getScheduledContent($contentId, $action),
                $when
            );
        }

        $createScheduledEntryStruct = new CreateScheduledEntryStruct();
        $createScheduledEntryStruct->userId = $this->permissionResolver->getCurrentUserReference()->getUserId();
        $createScheduledEntryStruct->contentId = $contentId;
        $createScheduledEntryStruct->actionTimestamp = $when->getTimestamp();
        $createScheduledEntryStruct->action = $action;
        $createScheduledEntryStruct->urlRoot = '';

        $this->persistenceHandler->insertContentEntry($createScheduledEntryStruct);

        return $this->getScheduledContent($contentId, $action);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateScheduledEntry(ScheduledEntry $scheduledEntry, DateTimeInterface $when): ScheduledEntry
    {
        $this->updateEntry($scheduledEntry, $when);

        return $this->getScheduledContent($scheduledEntry->content->id, $scheduledEntry->action);
    }

    public function unscheduleContent(int $contentId, string $action): bool
    {
        return $this->persistenceHandler->deleteContentEntry($contentId, $action);
    }

    public function getScheduledContent(int $contentId, string $action): ?ScheduledEntry
    {
        $spiScheduledEntry = $this->persistenceHandler->getContentEntry(
            $contentId,
            $action
        );

        return $this->buildScheduledDomainObject($spiScheduledEntry);
    }

    public function countScheduledEntries(string $action): int
    {
        return $this->persistenceHandler->countContentEntries($action);
    }

    public function countScheduledEntriesToProcess(string $action): int
    {
        $now = new \DateTime();

        return $this->persistenceHandler->countContentsEntriesOlderThan(
            (int)$now->format('U'),
            $action
        );
    }

    public function getScheduledEntriesToProcess(string $action, int $limit = 25): iterable
    {
        $now = new \DateTime();

        return $this->buildScheduledDomainObjectList(
            $this->persistenceHandler->getContentsEntriesOlderThan(
                (int)$now->format('U'),
                $action,
                0,
                $limit
            )
        );
    }

    public function countScheduledEntriesInDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        string $action,
        array $languages = [],
        ?int $sinceId = null
    ): int {
        return $this->persistenceHandler->countContentsEntriesInDateRange(
            (int)$start->format('U'),
            (int)$end->format('U'),
            $action,
            $this->getLanguagesIds($languages),
            $sinceId
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
        /** @var \Ibexa\Scheduler\ValueObject\ScheduledEntry[] $spiScheduledEntries */
        $spiScheduledEntries = $this->persistenceHandler->getContentsEntriesByDateRange(
            (int)$start->format('U'),
            (int)$end->format('U'),
            $action,
            $this->getLanguagesIds($languages),
            $sinceId,
            $limit
        );

        return $this->buildScheduledDomainObjectList($spiScheduledEntries);
    }
}

class_alias(DateBasedContentStrategy::class, 'EzSystems\DateBasedPublisher\Core\Repository\DateBasedContentStrategy');
