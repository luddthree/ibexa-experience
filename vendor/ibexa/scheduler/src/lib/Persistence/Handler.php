<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Persistence;

use Ibexa\Scheduler\ValueObject\CreateScheduledEntryStruct;
use Ibexa\Scheduler\ValueObject\ScheduledEntry;
use Ibexa\Scheduler\ValueObject\UpdateScheduledEntry;

/**
 * Handler for DateBasedPublisher Storage Engine.
 */
class Handler implements HandlerInterface
{
    /** @var \Ibexa\Scheduler\Persistence\HandlerInterface */
    protected $gateway;

    /**
     * Handler constructor.
     *
     * @param \Ibexa\Scheduler\Persistence\HandlerInterface $gateway
     */
    public function __construct(HandlerInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    public function insertVersionEntry(
        CreateScheduledEntryStruct $createScheduledEntryStruct
    ): bool {
        return $this->gateway->insertVersionEntry($createScheduledEntryStruct);
    }

    public function insertContentEntry(
        CreateScheduledEntryStruct $createScheduledEntryStruct
    ): bool {
        return $this->gateway->insertContentEntry($createScheduledEntryStruct);
    }

    public function updateEntry(
        UpdateScheduledEntry $updateScheduledEntry
    ): bool {
        return $this->gateway->updateEntry($updateScheduledEntry);
    }

    public function hasVersionEntry(
        int $versionId,
        ?string $action
    ): bool {
        return $this->gateway->hasVersionEntry($versionId, $action);
    }

    public function hasContentEntry(
        int $contentId,
        ?string $action
    ): bool {
        return $this->gateway->hasContentEntry($contentId, $action);
    }

    public function getVersionEntry(int $versionId, ?string $action): ?ScheduledEntry
    {
        return $this->gateway->getVersionEntry($versionId, $action);
    }

    public function getContentEntry(int $contentId, ?string $action): ?ScheduledEntry
    {
        return $this->gateway->getContentEntry($contentId, $action);
    }

    public function getEntriesByIds(array $scheduleVersionIds): iterable
    {
        return $this->gateway->getEntriesByIds($scheduleVersionIds);
    }

    public function getVersionsEntries(?string $action, int $page = 0, int $limit = 25): array
    {
        return $this->gateway->getVersionsEntries($action, $page, $limit);
    }

    public function getUserVersionsEntries(
        int $userId,
        ?string $action,
        int $page = 0,
        int $limit = 25
    ): iterable {
        return $this->gateway->getUserVersionsEntries($userId, $action, $page, $limit);
    }

    public function getAllTypesEntries(
        int $contentId,
        ?string $action,
        int $page = 0,
        ?int $limit = 25
    ): iterable {
        return $this->gateway->getAllTypesEntries($contentId, $action, $page, $limit);
    }

    public function getVersionsEntriesOlderThan(
        int $timestamp,
        ?string $action,
        int $page = 0,
        int $limit = 25
    ): iterable {
        return $this->gateway->getVersionsEntriesOlderThan($timestamp, $action, $page, $limit);
    }

    public function getContentsEntriesOlderThan(
        int $timestamp,
        ?string $action,
        int $page = 0,
        int $limit = 25
    ): iterable {
        return $this->gateway->getContentsEntriesOlderThan($timestamp, $action, $page, $limit);
    }

    public function getVersionsEntriesByDateRange(
        int $start,
        int $end,
        ?string $action,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable {
        return $this->gateway->getVersionsEntriesByDateRange(
            $start,
            $end,
            $action,
            $languages,
            $sinceId,
            $limit
        );
    }

    public function getContentsEntriesByDateRange(
        int $start,
        int $end,
        ?string $action,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable {
        return $this->gateway->getContentsEntriesByDateRange(
            $start,
            $end,
            $action,
            $languages,
            $sinceId,
            $limit
        );
    }

    public function getVersionsEntriesForContent(
        int $contentId,
        ?string $action,
        int $page = 0,
        int $limit = 25
    ): iterable {
        return $this->gateway->getVersionsEntriesForContent($contentId, $action, $page, $limit);
    }

    public function countVersionsEntries(?string $action): int
    {
        return $this->gateway->countVersionsEntries($action);
    }

    public function countContentEntries(?string $action): int
    {
        return $this->gateway->countContentEntries($action);
    }

    public function countVersionsEntriesForContent(
        int $contentId,
        ?string $action
    ): int {
        return $this->gateway->countVersionsEntriesForContent($contentId, $action);
    }

    public function countUserVersionsEntries(int $userId, ?string $action): int
    {
        return $this->gateway->countUserVersionsEntries($userId, $action);
    }

    public function countContentsEntriesOlderThan(int $timestamp, ?string $action): int
    {
        return $this->gateway->countContentsEntriesOlderThan($timestamp, $action);
    }

    public function countVersionsEntriesOlderThan(int $timestamp, ?string $action): int
    {
        return $this->gateway->countVersionsEntriesOlderThan($timestamp, $action);
    }

    public function countVersionsEntriesInDateRange(
        int $start,
        int $end,
        ?string $action,
        array $languages = [],
        ?int $sinceId = null
    ): int {
        return $this->gateway->countVersionsEntriesInDateRange(
            $start,
            $end,
            $action,
            $languages,
            $sinceId
        );
    }

    public function countContentsEntriesInDateRange(
        int $start,
        int $end,
        ?string $action,
        array $languages = [],
        ?int $sinceId = null
    ): int {
        return $this->gateway->countContentsEntriesInDateRange(
            $start,
            $end,
            $action,
            $languages,
            $sinceId
        );
    }

    public function deleteVersionEntry(int $versionId, ?string $action): bool
    {
        return $this->gateway->deleteVersionEntry($versionId, $action);
    }

    public function deleteContentEntry(int $contentId, ?string $action): bool
    {
        return $this->gateway->deleteContentEntry($contentId, $action);
    }

    public function deleteAllTypesEntries(int $contentId, ?string $action): bool
    {
        return $this->gateway->deleteAllTypesEntries($contentId, $action);
    }
}

class_alias(Handler::class, 'EzSystems\DateBasedPublisher\Core\Persistence\Handler');
