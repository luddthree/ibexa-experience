<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Event;

use Ibexa\Bundle\Migration\Event\ObjectRelationListFieldTypeSubscriber;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Migration\Event\FieldValueFromHashEvent;
use PHPUnit\Framework\TestCase;

final class ObjectRelationListFieldTypeSubscriberTest extends TestCase
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\LocationService&\PHPUnit\Framework\MockObject\MockObject
     */
    private $locationService;

    /**
     * @var \Ibexa\Bundle\Migration\Event\ObjectRelationListFieldTypeSubscriber
     */
    private $subscriber;

    protected function setUp(): void
    {
        $this->locationService = $this->createMock(LocationService::class);
        $this->subscriber = new ObjectRelationListFieldTypeSubscriber($this->locationService);
    }

    public function testConversionDoesNotAffectOtherFieldTypes(): void
    {
        $this->locationService
            ->expects(self::never())
            ->method('loadLocation');

        $this->locationService
            ->expects(self::never())
            ->method('loadLocationByRemoteId');

        $event = new FieldValueFromHashEvent(
            'foo_field_type',
            [],
            [],
        );

        $this->subscriber->convertLocationIds($event);

        self::assertSame([], $event->getHash());
    }

    public function testConversionWorksWithoutLocationFields(): void
    {
        $hash = [
            'destinationContentIds' => [
                42,
            ],
        ];
        $event = new FieldValueFromHashEvent(
            'ezobjectrelationlist',
            [],
            $hash,
        );

        $this->subscriber->convertLocationIds($event);

        self::assertSame($hash, $event->getHash());
    }

    public function testConversionRemovesDuplicatesAndConservesOrderOfIds(): void
    {
        $this->locationService
            ->expects(self::exactly(2))
            ->method('loadLocation')
            ->withConsecutive(
                [66],
                [33],
            )
            ->willReturnOnConsecutiveCalls(
                $this->createLocationMock(55),
                $this->createLocationMock(42),
            );

        $this->locationService
            ->expects(self::exactly(2))
            ->method('loadLocationByRemoteId')
            ->withConsecutive(
                ['bar_remote_id'],
                ['foo_remote_id'],
            )
            ->willReturnOnConsecutiveCalls(
                $this->createLocationMock(42),
                $this->createLocationMock(99),
            );

        $hash = [
            'destinationContentIds' => [
                88,
                42,
            ],
            'locationIds' => [
                66,
                33,
            ],
            'locationRemoteIds' => [
                'bar_remote_id',
                'foo_remote_id',
            ],
        ];
        $event = new FieldValueFromHashEvent(
            'ezobjectrelationlist',
            [],
            $hash,
        );

        $this->subscriber->convertLocationIds($event);

        self::assertSame([
            'destinationContentIds' => [
                88,
                42,
                55,
                99,
            ],
            'locationIds' => [
                66,
                33,
            ],
            'locationRemoteIds' => [
                'bar_remote_id',
                'foo_remote_id',
            ],
        ], $event->getHash());
    }

    private function createLocationMock(int $contentId): Location
    {
        return new Location([
            'contentInfo' => new ContentInfo([
                'id' => $contentId,
            ]),
        ]);
    }
}
