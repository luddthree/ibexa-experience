<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ActivityLog\EventSubscriber\PostActivityListLoad;

use Generator;
use Ibexa\Bundle\ActivityLog\EventSubscriber\PostActivityListLoad\ContentActivity;
use Ibexa\Contracts\ActivityLog\Event\PostActivityGroupListLoadEvent;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityGroupListInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Throwable;
use Traversable;

final class ContentActivityTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService&\PHPUnit\Framework\MockObject\MockObject */
    private ContentService $contentService;

    private ContentActivity $subscriber;

    protected function setUp(): void
    {
        $this->contentService = $this->createMock(ContentService::class);
        $this->subscriber = new ContentActivity($this->contentService);
    }

    public function testLoadingContentActivity(): void
    {
        $logGenerator = $this->createLogGenerator([
            ['class' => Content::class, 'id' => '10', 'related_object_set' => true],
            ['class' => Content::class, 'id' => '10', 'related_object_set' => true],
        ]);
        $list = $this->createList($logGenerator);

        $this->contentService
            ->expects(self::once())
            ->method('loadContent')
            ->with('10');

        $this->subscriber->loadContent(new PostActivityGroupListLoadEvent($list));
    }

    public function testSuppressingAuthenticationExceptions(): void
    {
        $logGenerator = $this->createLogGenerator([
            ['class' => Content::class, 'id' => '10'],
            ['class' => Content::class, 'id' => '10'],
        ]);
        $list = $this->createList($logGenerator);

        $this->contentService
            ->expects(self::once())
            ->method('loadContent')
            ->with('10')
            ->willThrowException($this->createMock(UnauthorizedException::class));

        $this->subscriber->loadContent(new PostActivityGroupListLoadEvent($list));
    }

    public function testSuppressingNotFoundExceptions(): void
    {
        $logGenerator = $this->createLogGenerator([
            ['class' => Content::class, 'id' => '10'],
            ['class' => Content::class, 'id' => '10'],
        ]);
        $list = $this->createList($logGenerator);

        $this->contentService
            ->expects(self::once())
            ->method('loadContent')
            ->with('10')
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->subscriber->loadContent(new PostActivityGroupListLoadEvent($list));
    }

    public function testOtherExceptions(): void
    {
        $logGenerator = $this->createLogGenerator([
            ['class' => Content::class, 'id' => '10'],
            ['class' => Content::class, 'id' => '10'],
        ]);
        $list = $this->createList($logGenerator);

        $this->contentService
            ->expects(self::once())
            ->method('loadContent')
            ->with('10')
            ->willThrowException($this->createMock(Throwable::class));

        $this->expectException(Throwable::class);
        $this->subscriber->loadContent(new PostActivityGroupListLoadEvent($list));
    }

    public function testSkipsUnknownObjectClasses(): void
    {
        $logGenerator = $this->createLogGenerator([
            ['class' => 'foo', 'id' => '10'],
            ['class' => 'bar', 'id' => '42'],
        ]);
        $list = $this->createList($logGenerator);

        $this->contentService
            ->expects(self::never())
            ->method('loadContent');

        $this->subscriber->loadContent(new PostActivityGroupListLoadEvent($list));
    }

    public function testLogsInvalidContentIds(): void
    {
        $logGenerator = $this->createLogGenerator([
            ['class' => Content::class, 'id' => 'foo', 'related_object_set' => false],
            ['class' => Content::class, 'id' => 'foo', 'related_object_set' => false],
            ['class' => Content::class, 'id' => '10', 'related_object_set' => true],
        ]);
        $list = $this->createList($logGenerator);

        $this->contentService
            ->expects(self::once())
            ->method('loadContent');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('warning')
            ->with('Failed to load Content using ID: "foo". Content ID has to be an integerish value.');

        $this->subscriber->setLogger($logger);

        $this->subscriber->loadContent(new PostActivityGroupListLoadEvent($list));
    }

    /**
     * @param \Traversable<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface> $listIterator
     *
     * @return \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityGroupListInterface
     */
    private function createList(Traversable $listIterator): ActivityGroupListInterface
    {
        $list = $this->createMock(ActivityGroupListInterface::class);

        $list
            ->expects(self::once())
            ->method('getIterator')
            ->willReturn($listIterator);

        return $list;
    }

    /**
     * @param array<array{class: string, id: string, related_object_set?: bool}> $list
     *
     * @return \Generator<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface>
     */
    private function createLogGenerator(array $list): Generator
    {
        foreach ($list as $data) {
            $group = $this->createMock(ActivityLogGroupInterface::class);

            $log = $this->createMock(ActivityLogInterface::class);
            $log->method('getObjectClass')
                ->willReturn($data['class']);

            $log->method('getObjectId')
                ->willReturn($data['id']);

            $relatedObjectSet = $data['related_object_set'] ?? false;
            $log->expects($relatedObjectSet ? self::once() : self::never())
                ->method('setRelatedObject');

            $group->method('getActivityLogs')
                ->willReturn([$log]);

            yield $group;
        }
    }
}
