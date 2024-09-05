<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Event;

use Ibexa\Bundle\Migration\Event\ObjectRelationFieldTypeSubscriber;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\FieldType\Relation\Value as RelationValue;
use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Migration\Event\FieldValueFromHashEvent;
use Ibexa\Migration\Event\HashFromFieldValueEvent;
use PHPUnit\Framework\TestCase;

final class ObjectRelationFieldTypeSubscriberTest extends TestCase
{
    private ObjectRelationFieldTypeSubscriber $subscriber;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService&\PHPUnit\Framework\MockObject\MockObject */
    private ContentService $contentService;

    protected function setUp(): void
    {
        $this->contentService = $this->createMock(ContentService::class);
        $this->subscriber = new ObjectRelationFieldTypeSubscriber($this->contentService);
    }

    public function testConvertRemoteContentId(): void
    {
        $this->contentService
            ->method('loadContentByRemoteId')
            ->with('remote_content_id')
            ->willReturn($this->createContentMock(4, 'remote_content_id'));

        $event = new FieldValueFromHashEvent(
            'ezobjectrelation',
            [],
            ['contentRemoteId' => 'remote_content_id'],
        );

        $this->subscriber->convertRemoteContentId($event);

        self::assertEquals(
            [
                'destinationContentId' => 4,
                'contentRemoteId' => 'remote_content_id',
            ],
            $event->getHash()
        );
    }

    public function testConvertOnlyIfRelationFieldType(): void
    {
        $this->contentService
            ->expects(self::never())
            ->method('loadContentByRemoteId');

        $event = new FieldValueFromHashEvent(
            'foo_field_type',
            [],
            [],
        );

        $this->subscriber->convertRemoteContentId($event);

        self::assertSame([], $event->getHash());
    }

    public function testAddRemoteContentIdToHash(): void
    {
        $this->contentService
            ->method('loadContent')
            ->with(4)
            ->willReturn($this->createContentMock(4, 'remote_content_id'));

        $event = new HashFromFieldValueEvent(
            new RelationValue(4)
        );

        $this->subscriber->addRemoteContentId($event);

        self::assertEquals(['contentRemoteId' => 'remote_content_id'], $event->getHash());
    }

    /**
     * @dataProvider provideHashFromFieldValueInvalidEvent
     */
    public function testAddToHashOnlyIfFieldType(HashFromFieldValueEvent $event): void
    {
        $this->contentService
            ->expects(self::never())
            ->method('loadContent');

        $this->subscriber->addRemoteContentId($event);

        self::assertNull($event->getHash());
    }

    /**
     * @return \Ibexa\Migration\Event\HashFromFieldValueEvent[][]
     */
    public function provideHashFromFieldValueInvalidEvent(): array
    {
        return [
            'text line value' => [
                new HashFromFieldValueEvent(
                    new TextLineValue()
                ),
            ],
            'empty relation' => [
                new HashFromFieldValueEvent(
                    new RelationValue()
                ),
            ],
        ];
    }

    private function createContentMock(int $contentId, string $remoteContentId): Content
    {
        return new Content([
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo([
                    'id' => $contentId,
                    'remoteId' => $remoteContentId,
                ]),
            ]),
        ]);
    }
}
