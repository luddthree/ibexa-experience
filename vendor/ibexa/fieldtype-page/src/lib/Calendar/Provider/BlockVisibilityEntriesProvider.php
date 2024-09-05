<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Calendar\Provider;

use DateTimeInterface;
use Ibexa\FieldTypePage\Calendar\BlockVisibilityEntryBuilder;
use Ibexa\FieldTypePage\Persistence\EntriesHandlerInterface;

final class BlockVisibilityEntriesProvider implements EntriesProviderInterface
{
    /** @var \Ibexa\FieldTypePage\Persistence\EntriesHandlerInterface */
    private $persistenceHandler;

    /** @var \Ibexa\FieldTypePage\Calendar\BlockVisibilityEntryBuilder */
    private $entryBuilder;

    public function __construct(
        EntriesHandlerInterface $persistenceHandler,
        BlockVisibilityEntryBuilder $entryBuilder
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->entryBuilder = $entryBuilder;
    }

    /**
     * @return \Ibexa\FieldTypePage\Calendar\ScheduledVisibilityEntry[]
     */
    public function getScheduledEntriesByIds(array $scheduleVersionIds): iterable
    {
        return $this->buildBlockEntryList(
            $this->persistenceHandler->getEntriesByIds($scheduleVersionIds)
        );
    }

    public function countScheduledEntries(): int
    {
        return $this->persistenceHandler->countVersionsEntries();
    }

    /**
     * @return \Ibexa\FieldTypePage\Calendar\ScheduledVisibilityEntry[]
     */
    public function getScheduledEntriesInDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable {
        /** @var \Ibexa\FieldTypePage\Calendar\ScheduledVisibilityEntry[] $scheduledVersions */
        $scheduledVersions = $this->persistenceHandler->getVersionsEntriesByDateRange(
            (int)$start->format('U'),
            (int)$end->format('U'),
            $this->getLanguagesIds($languages),
            $sinceId,
            $limit
        );

        return $this->buildBlockEntryList($scheduledVersions);
    }

    public function countScheduledEntriesInDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        array $languages = [],
        ?int $sinceId = null
    ): int {
        return $this->persistenceHandler->countVersionsEntriesInDateRange(
            (int)$start->format('U'),
            (int)$end->format('U'),
            $this->getLanguagesIds($languages),
            $sinceId
        );
    }

    private function getLanguagesIds(array $languages): array
    {
        return array_column($languages, 'id');
    }

    /**
     * @param \Ibexa\FieldTypePage\Persistence\BlockEntry[] $blockEntries
     */
    private function buildBlockEntryList(iterable $blockEntries): iterable
    {
        $eventList = [];
        foreach ($blockEntries as $blockEntry) {
            $event = $this->entryBuilder->build($blockEntry);

            if (null !== $event) {
                $eventList[] = $event;
            }
        }

        return $eventList;
    }
}

class_alias(BlockVisibilityEntriesProvider::class, 'EzSystems\EzPlatformPageFieldType\Calendar\Provider\BlockVisibilityEntriesProvider');
