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
 * DateBasedPublisher handler interface.
 */
interface HandlerInterface
{
    /**
     * @param \Ibexa\Scheduler\ValueObject\CreateScheduledEntryStruct $createScheduledEntryStruct
     *
     * @return bool
     */
    public function insertVersionEntry(
        CreateScheduledEntryStruct $createScheduledEntryStruct
    ): bool;

    /**
     * @param \Ibexa\Scheduler\ValueObject\CreateScheduledEntryStruct $createScheduledEntryStruct
     *
     * @return bool
     */
    public function insertContentEntry(
        CreateScheduledEntryStruct $createScheduledEntryStruct
    ): bool;

    /**
     * @param \Ibexa\Scheduler\ValueObject\UpdateScheduledEntry $updateScheduledEntry
     *
     * @return bool
     */
    public function updateEntry(
        UpdateScheduledEntry $updateScheduledEntry
    ): bool;

    public function hasVersionEntry(
        int $versionId,
        ?string $action
    ): bool;

    public function hasContentEntry(
        int $contentId,
        ?string $action
    ): bool;

    /**
     * @return \Ibexa\Scheduler\ValueObject\ScheduledEntry|null
     */
    public function getVersionEntry(
        int $versionId,
        ?string $action
    ): ?ScheduledEntry;

    /**
     * @return \Ibexa\Scheduler\ValueObject\ScheduledEntry|null
     */
    public function getContentEntry(
        int $contentId,
        ?string $action
    ): ?ScheduledEntry;

    /**
     * @param int[] $scheduleVersionIds
     *
     * @return \Ibexa\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getEntriesByIds(array $scheduleVersionIds): iterable;

    /**
     * @return \Ibexa\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getVersionsEntries(
        ?string $action,
        int $page = 0,
        int $limit = 25
    ): iterable;

    /**
     * @return \Ibexa\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getUserVersionsEntries(
        int $userId,
        ?string $action,
        int $page = 0,
        int $limit = 25
    ): iterable;

    /**
     * @return \Ibexa\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getAllTypesEntries(
        int $contentId,
        ?string $action,
        int $page = 0,
        ?int $limit = 25
    ): iterable;

    /**
     * @return \Ibexa\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getVersionsEntriesOlderThan(
        int $timestamp,
        ?string $action,
        int $page = 0,
        int $limit = 25
    ): iterable;

    /**
     * @return \Ibexa\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getContentsEntriesOlderThan(
        int $timestamp,
        ?string $action,
        int $page = 0,
        int $limit = 25
    ): iterable;

    /**
     * @param int[] $languages
     *
     * @return \Ibexa\Scheduler\ValueObject\ScheduledEntry[]
     */
    public function getVersionsEntriesByDateRange(
        int $start,
        int $end,
        ?string $action,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable;

    public function getContentsEntriesByDateRange(
        int $start,
        int $end,
        ?string $action,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable;

    public function getVersionsEntriesForContent(
        int $contentId,
        ?string $action,
        int $page = 0,
        int $limit = 25
    ): iterable;

    public function countVersionsEntries(?string $action): int;

    public function countContentEntries(?string $action): int;

    public function countVersionsEntriesForContent(int $contentId, ?string $action): int;

    public function countUserVersionsEntries(int $userId, ?string $action): int;

    public function countContentsEntriesOlderThan(int $timestamp, ?string $action): int;

    public function countVersionsEntriesOlderThan(int $timestamp, ?string $action): int;

    public function countVersionsEntriesInDateRange(
        int $start,
        int $end,
        ?string $action,
        array $languages = [],
        ?int $sinceId = null
    ): int;

    public function countContentsEntriesInDateRange(
        int $start,
        int $end,
        ?string $action,
        array $languages = [],
        ?int $sinceId = null
    ): int;

    public function deleteVersionEntry(int $versionId, ?string $action): bool;

    public function deleteContentEntry(int $contentId, ?string $action): bool;

    public function deleteAllTypesEntries(int $contentId, ?string $action): bool;
}

class_alias(HandlerInterface::class, 'EzSystems\DateBasedPublisher\SPI\Persistence\HandlerInterface');
