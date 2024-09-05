<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Repository;

use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Scheduler\Repository\DateBasedHideServiceInterface;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Scheduler\Schedule\DateBasedContentStrategyInterface;

final class DateBasedHideService extends AbstractDateBasedService implements DateBasedHideServiceInterface
{
    public const ACTION_TYPE = 'hide';

    /**
     * @var \Ibexa\Scheduler\Schedule\DateBasedContentStrategyInterface
     */
    private $dateBasedContentStrategy;

    public function __construct(
        DateBasedContentStrategyInterface $dateBasedContentStrategy,
        ContentService $contentService,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($contentService, $permissionResolver);

        $this->dateBasedContentStrategy = $dateBasedContentStrategy;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function scheduleHide(int $contentId, DateTimeInterface $when): ScheduledEntry
    {
        $content = $this->contentService->loadContent($contentId);

        if (!$this->permissionResolver->canUser('content', 'hide', $content)) {
            throw new UnauthorizedException('content', 'hide', ['contentId' => $content->id]);
        }

        return $this->dateBasedContentStrategy->scheduleContent(
            $contentId,
            $when,
            $this->getActionType()
        );
    }

    public function isScheduledHide(int $contentId): bool
    {
        return $this->dateBasedContentStrategy->isContentScheduled(
            $contentId,
            $this->getActionType()
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateScheduledHide(ScheduledEntry $scheduledEntry, DateTimeInterface $when): ScheduledEntry
    {
        if (!$this->permissionResolver->canUser('content', 'hide', $scheduledEntry->content)) {
            throw new UnauthorizedException('content', 'hide', ['contentId' => $scheduledEntry->content->id]);
        }

        return $this->dateBasedContentStrategy->updateScheduledEntry($scheduledEntry, $when);
    }

    public function unscheduleHide(int $contentId): bool
    {
        return $this->dateBasedContentStrategy->unscheduleContent(
            $contentId,
            $this->getActionType()
        );
    }

    public function getScheduledEntriesByIds(array $scheduledEntriesIds): iterable
    {
        return $this->dateBasedContentStrategy->getScheduledEntriesByIds(
            $scheduledEntriesIds
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getScheduledHide(int $contentId): ScheduledEntry
    {
        $scheduledEntry = $this->dateBasedContentStrategy->getScheduledContent(
            $contentId,
            $this->getActionType()
        );

        if (empty($scheduledEntry)) {
            throw new NotFoundException('ScheduledEntry', sprintf('type: %s, contentId: %d', $this->getActionType(), $contentId));
        }

        return $scheduledEntry;
    }

    public function getScheduledEntriesToProcess(int $limit = 25): iterable
    {
        return $this->dateBasedContentStrategy->getScheduledEntriesToProcess(
            $this->getActionType(),
            $limit
        );
    }

    public function getScheduledEntriesInDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable {
        return $this->dateBasedContentStrategy->getScheduledEntriesInDateRange(
            $start,
            $end,
            $this->getActionType(),
            $languages,
            $sinceId,
            $limit
        );
    }

    public function countScheduledEntries(): int
    {
        return $this->dateBasedContentStrategy->countScheduledEntries(
            $this->getActionType()
        );
    }

    public function countScheduledEntriesToProcess(): int
    {
        return $this->dateBasedContentStrategy->countScheduledEntriesToProcess(
            $this->getActionType()
        );
    }

    public function countScheduledEntriesInDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        array $languages = [],
        ?int $sinceId = null
    ): int {
        return $this->dateBasedContentStrategy->countScheduledEntriesInDateRange(
            $start,
            $end,
            $this->getActionType(),
            $languages,
            $sinceId
        );
    }

    protected function getActionType(): string
    {
        return self::ACTION_TYPE;
    }
}

class_alias(DateBasedHideService::class, 'EzSystems\DateBasedPublisher\Core\Repository\DateBasedHideService');
