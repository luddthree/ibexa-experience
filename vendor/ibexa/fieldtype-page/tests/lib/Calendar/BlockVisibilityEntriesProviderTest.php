<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\Calendar;

use DateTime;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\User\User;
use Ibexa\FieldTypePage\Calendar\BlockVisibilityEntryBuilder;
use Ibexa\FieldTypePage\Calendar\Provider\BlockVisibilityEntriesProvider;
use Ibexa\FieldTypePage\Calendar\ScheduledVisibilityEntry;
use Ibexa\FieldTypePage\Persistence\BlockEntry;
use Ibexa\FieldTypePage\Persistence\EntriesHandlerInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class BlockVisibilityEntriesProviderTest extends TestCase
{
    private const LANGUAGE_ID = 111;

    /** @var \Ibexa\FieldTypePage\Calendar\Provider\BlockVisibilityEntriesProvider */
    private $blockVisibilityEntriesProvider;

    /** @var \Ibexa\FieldTypePage\Persistence\EntriesHandlerInterface */
    private $persistenceHandler;

    /** @var \Ibexa\FieldTypePage\Calendar\BlockVisibilityEntryBuilder */
    private $entryBuilder;

    protected function setUp(): void
    {
        $this->persistenceHandler = $this->createMock(EntriesHandlerInterface::class);
        $this->entryBuilder = $this->createMock(BlockVisibilityEntryBuilder::class);

        $this->blockVisibilityEntriesProvider = new BlockVisibilityEntriesProvider(
            $this->persistenceHandler,
            $this->entryBuilder
        );
    }

    public function testGetScheduledEntriesByIds()
    {
        $blockEntry1 = new BlockEntry(['id' => '__ID1__']);
        $blockEntry2 = new BlockEntry(['id' => '__ID2__']);
        $ids = [
            $blockEntry1->id,
            $blockEntry2->id,
        ];
        $this->persistenceHandler
            ->method('getEntriesByIds')
            ->with($this->equalTo($ids))
            ->willReturn([
                $blockEntry1,
                $blockEntry2,
            ]);

        $expectedEntries = $this->createScheduledVisibilityEntry();
        $this->entryBuilder
            ->method('build')
            ->withConsecutive(
                [$this->equalTo($blockEntry1)],
                [$this->equalTo($blockEntry2)]
            )
            ->willReturnOnConsecutiveCalls(
                null,
                $expectedEntries
            );

        $fetchedEntries = $this->blockVisibilityEntriesProvider->getScheduledEntriesByIds($ids);

        Assert::assertEquals([$expectedEntries], $fetchedEntries);
    }

    public function testCountScheduledEntries()
    {
        $expectedEntityCounter = 23;
        $this->persistenceHandler
            ->method('countVersionsEntries')
            ->willReturn($expectedEntityCounter);

        $entityCounter = $this->blockVisibilityEntriesProvider->countScheduledEntries();

        Assert::assertEquals($expectedEntityCounter, $entityCounter);
    }

    public function testGetScheduledEntriesInDateRange()
    {
        $blockEntry1 = new BlockEntry(['id' => '__ID1__']);
        $blockEntry2 = new BlockEntry(['id' => '__ID2__']);

        $fromDate = DateTime::createFromFormat('d/m/Y', '1/10/2020');
        $toDate = DateTime::createFromFormat('d/m/Y', '15/10/2020');
        $languages = [
            ['id' => self::LANGUAGE_ID],
        ];
        $sinceId = 123;
        $limit = 25;
        $this->persistenceHandler
            ->method('getVersionsEntriesByDateRange')
            ->with(
                $this->equalTo((int)$fromDate->format('U')),
                $this->equalTo((int)$toDate->format('U')),
                $this->equalTo([self::LANGUAGE_ID]),
                $this->equalTo($sinceId),
                $this->equalTo($limit)
            )
            ->willReturn([
                $blockEntry1,
                $blockEntry2,
            ]);

        $expectedEntries = $this->createScheduledVisibilityEntry();
        $this->entryBuilder
            ->method('build')
            ->withConsecutive(
                [$this->equalTo($blockEntry1)],
                [$this->equalTo($blockEntry2)]
            )
            ->willReturnOnConsecutiveCalls(
                null,
                $expectedEntries
            );

        $fetchedEntries = $this->blockVisibilityEntriesProvider->getScheduledEntriesInDateRange(
            $fromDate,
            $toDate,
            $languages,
            $sinceId,
            $limit
        );

        Assert::assertEquals([$expectedEntries], $fetchedEntries);
    }

    public function testCountScheduledEntriesInDateRange()
    {
        $expectedEntityCounter = 23;

        $fromDate = DateTime::createFromFormat('d/m/Y', '1/10/2020');
        $toDate = DateTime::createFromFormat('d/m/Y', '15/10/2020');
        $languages = [
            ['id' => self::LANGUAGE_ID],
        ];
        $sinceId = 123;

        $this->persistenceHandler
            ->method('countVersionsEntriesInDateRange')
            ->with(
                $this->equalTo((int)$fromDate->format('U')),
                $this->equalTo((int)$toDate->format('U')),
                $this->equalTo([self::LANGUAGE_ID]),
                $this->equalTo($sinceId)
            )
            ->willReturn($expectedEntityCounter);

        $entityCounter = $this->blockVisibilityEntriesProvider->countScheduledEntriesInDateRange(
            $fromDate,
            $toDate,
            $languages,
            $sinceId,
        );

        Assert::assertEquals($expectedEntityCounter, $entityCounter);
    }

    private function createScheduledVisibilityEntry(): ScheduledVisibilityEntry
    {
        return new ScheduledVisibilityEntry(
            '__ENTRY_ID__',
            new DateTime(),
            new Language(),
            new User(),
            new Content(),
            '__BLOCK_NAME__',
            '__BLOCK_TYPE__'
        );
    }
}

class_alias(BlockVisibilityEntriesProviderTest::class, 'EzSystems\EzPlatformPageFieldType\Tests\Calendar\BlockVisibilityEntriesProviderTest');
