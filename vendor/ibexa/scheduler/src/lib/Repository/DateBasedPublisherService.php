<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\Repository;

use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Scheduler\Event\ScheduledPublish;
use Ibexa\Scheduler\Schedule\DateBasedVersionStrategyInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class DateBasedPublisherService extends AbstractDateBasedService implements DateBasedPublishServiceInterface
{
    public const ACTION_TYPE = 'publish';

    /** @var \Ibexa\Scheduler\Schedule\DateBasedVersionStrategyInterface */
    private $dateBasedVersionStrategy;

    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        DateBasedVersionStrategyInterface $dateBasedVersionStrategy,
        ContentService $contentService,
        PermissionResolver $permissionResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($contentService, $permissionResolver);

        $this->dateBasedVersionStrategy = $dateBasedVersionStrategy;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function schedulePublish(VersionInfo $versionInfo, DateTimeInterface $when): ScheduledEntry
    {
        $contentInfo = $versionInfo->getContentInfo();
        $content = $this->contentService->loadContentByContentInfo($contentInfo);

        if (!$this->permissionResolver->canUser('content', 'publish', $content)) {
            throw new UnauthorizedException('content', 'publish', ['contentId' => $content->id]);
        }

        if (!$contentInfo->published) {
            if (!$contentInfo->isDraft() && !$this->permissionResolver->canUser('content', 'create', $content)) {
                throw new UnauthorizedException('content', 'create', ['contentId' => $content->id]);
            }
        } elseif (!$this->permissionResolver->canUser('content', 'edit', $content)) {
            // If the content is published we must check an edit policy
            throw new UnauthorizedException('content', 'edit', ['contentId' => $content->id]);
        }

        if ($this->isScheduledPublish($versionInfo->id)) {
            throw new BadStateException('$versionInfo', sprintf(
                'version %d of Content item %d is already scheduled for publication.',
                $versionInfo->versionNo,
                $versionInfo->contentInfo->id
            ));
        }

        $scheduledEntry = $this->dateBasedVersionStrategy->scheduleVersion(
            $versionInfo,
            $when,
            $this->getActionType()
        );

        $this->eventDispatcher->dispatch(
            new ScheduledPublish($scheduledEntry)
        );

        return $scheduledEntry;
    }

    public function isScheduledPublish(int $versionId): bool
    {
        return $this->dateBasedVersionStrategy->isVersionScheduled(
            $versionId,
            $this->getActionType()
        );
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateScheduledPublish(ScheduledEntry $scheduledEntry, DateTimeInterface $when): ScheduledEntry
    {
        $versionInfo = $scheduledEntry->versionInfo;

        $contentInfo = $versionInfo->getContentInfo();
        $content = $this->contentService->loadContentByContentInfo($contentInfo);

        if (!$contentInfo->published) {
            if (!$this->permissionResolver->canUser('content', 'create', $content)) {
                throw new UnauthorizedException('content', 'create', ['contentId' => $content->id]);
            }
        } elseif (!$this->permissionResolver->canUser('content', 'edit', $content)) {
            throw new UnauthorizedException('content', 'edit', ['contentId' => $content->id]);
        }

        return $this->dateBasedVersionStrategy->updateScheduledEntry($scheduledEntry, $when);
    }

    public function unschedulePublish(int $versionId): bool
    {
        return $this->dateBasedVersionStrategy->unscheduleVersion(
            $versionId,
            $this->getActionType()
        );
    }

    public function getScheduledEntriesByIds(array $scheduledEntriesIds): iterable
    {
        return $this->dateBasedVersionStrategy->getScheduledEntriesByIds(
            $scheduledEntriesIds
        );
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function getScheduledPublish(int $versionId): ScheduledEntry
    {
        $scheduledEntry = $this->dateBasedVersionStrategy->getScheduledVersion(
            $versionId,
            $this->getActionType()
        );

        if (empty($scheduledEntry)) {
            throw new NotFoundException('ScheduledEntry', sprintf('type: %s, versionId: %d', $this->getActionType(), $versionId));
        }

        return $scheduledEntry;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionsEntriesForContent(int $contentId, int $page = 0, int $limit = 25): iterable
    {
        return $this->dateBasedVersionStrategy->getVersionsEntriesForContent(
            $contentId,
            $this->getActionType(),
            $page,
            $limit
        );
    }

    public function getScheduledVersions(int $page = 0, int $limit = 25): iterable
    {
        return $this->dateBasedVersionStrategy->getScheduledVersions(
            $this->getActionType(),
            $page,
            $limit
        );
    }

    public function getScheduledEntriesToProcess(int $limit = 25): iterable
    {
        return $this->dateBasedVersionStrategy->getScheduledEntriesToProcess(
            $this->getActionType(),
            $limit
        );
    }

    public function getUserScheduledVersions(int $page, int $limit): iterable
    {
        return $this->dateBasedVersionStrategy->getUserScheduledVersions(
            $this->getActionType(),
            $page,
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
        return $this->dateBasedVersionStrategy->getScheduledEntriesInDateRange(
            $start,
            $end,
            $this->getActionType(),
            $languages,
            $sinceId,
            $limit
        );
    }

    public function countScheduledEntriesToProcess(): int
    {
        return $this->dateBasedVersionStrategy->countScheduledEntriesToProcess(
            $this->getActionType()
        );
    }

    public function countUserScheduledVersions(): int
    {
        return $this->dateBasedVersionStrategy->countUserScheduledVersions(
            $this->getActionType()
        );
    }

    public function countScheduledEntries(): int
    {
        return $this->dateBasedVersionStrategy->countScheduledEntries(
            $this->getActionType()
        );
    }

    public function countScheduledEntriesInDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        array $languages = [],
        ?int $sinceId = null
    ): int {
        return $this->dateBasedVersionStrategy->countScheduledEntriesInDateRange(
            $start,
            $end,
            $this->getActionType(),
            $languages,
            $sinceId
        );
    }

    public function countVersionsEntriesForContent(int $contentId): int
    {
        return $this->dateBasedVersionStrategy->countVersionsEntriesForContent(
            $contentId,
            $this->getActionType()
        );
    }

    protected function getActionType(): string
    {
        return self::ACTION_TYPE;
    }
}

class_alias(DateBasedPublisherService::class, 'EzSystems\DateBasedPublisher\Core\Repository\DateBasedPublisherService');
