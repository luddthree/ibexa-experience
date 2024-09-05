<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Schedule;

use DateTime;
use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException as APIUnauthorizedException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;
use Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperInterface;
use Ibexa\Scheduler\Persistence\HandlerInterface;
use Ibexa\Scheduler\ValueObject\ScheduledEntry as SPIScheduledEntry;
use Ibexa\Scheduler\ValueObject\UpdateScheduledEntry;

abstract class AbstractDateBasedStrategy implements DateBasedEntriesListInterface
{
    /** @var \Ibexa\Scheduler\Persistence\HandlerInterface */
    protected $persistenceHandler;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    protected $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    protected $userService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperInterface */
    protected $proxyDomainMapper;

    public function __construct(
        HandlerInterface $persistenceHandler,
        PermissionResolver $permissionResolver,
        ContentService $contentService,
        UserService $userService,
        ProxyDomainMapperInterface $proxyDomainMapper
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->contentService = $contentService;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
        $this->proxyDomainMapper = $proxyDomainMapper;
    }

    public function getScheduledEntriesByIds(array $scheduleVersionIds): iterable
    {
        return $this->buildScheduledDomainObjectList(
            $this->persistenceHandler->getEntriesByIds($scheduleVersionIds)
        );
    }

    /**
     * @param \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry $scheduledEntry
     * @param \DateTimeInterface $when
     */
    protected function updateEntry(ScheduledEntry $scheduledEntry, DateTimeInterface $when): void
    {
        $updateScheduledEntryStruct = new UpdateScheduledEntry();
        $updateScheduledEntryStruct->id = $scheduledEntry->id;
        $updateScheduledEntryStruct->actionTimestamp = $when->getTimestamp();

        $this->persistenceHandler->updateEntry($updateScheduledEntryStruct);
    }

    /**
     * @param \Ibexa\Scheduler\ValueObject\ScheduledEntry[] $spiScheduledEntries
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[]
     */
    protected function buildScheduledDomainObjectList(iterable $spiScheduledEntries): array
    {
        $scheduledEntries = [];

        foreach ($spiScheduledEntries as $spiScheduledEntry) {
            try {
                $scheduledEntries[] = $this->buildScheduledDomainObject($spiScheduledEntry);
            } catch (APIUnauthorizedException | NotFoundException $e) {
                // intentionally skipped as we present only content which could be seen by certain user
            }
        }

        return $scheduledEntries;
    }

    /**
     * @param \Ibexa\Scheduler\ValueObject\ScheduledEntry|null $spiScheduledEntry
     *
     * @return \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry|null
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    protected function buildScheduledDomainObject(?SPIScheduledEntry $spiScheduledEntry): ?ScheduledEntry
    {
        if (null === $spiScheduledEntry) {
            return null;
        }

        try {
            $user = $this->userService->loadUser((int)$spiScheduledEntry->userId);
        } catch (NotFoundException $e) {
            $user = null;
        }

        $content = $this->proxyDomainMapper->createContentProxy(
            $spiScheduledEntry->contentId,
            Language::ALL,
            true,
            $spiScheduledEntry->versionNumber
        );

        $initData = [
            'id' => $spiScheduledEntry->id,
            'user' => $user,
            'content' => $content,
            'date' => DateTime::createFromFormat('U', (string)$spiScheduledEntry->actionTimestamp),
            'urlRoot' => $spiScheduledEntry->urlRoot,
            'action' => $spiScheduledEntry->action,
        ];

        if (!empty($spiScheduledEntry->versionNumber)) {
            $initData['versionInfo'] = $this->contentService->loadVersionInfoById(
                (int)$spiScheduledEntry->contentId,
                (int)$spiScheduledEntry->versionNumber
            );
        }

        return new ScheduledEntry($initData);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language[] $languages
     *
     * @return int[]
     */
    protected function getLanguagesIds(array $languages): array
    {
        return array_column($languages, 'id');
    }
}

class_alias(AbstractDateBasedStrategy::class, 'EzSystems\DateBasedPublisher\SPI\Schedule\AbstractDateBasedStrategy');
