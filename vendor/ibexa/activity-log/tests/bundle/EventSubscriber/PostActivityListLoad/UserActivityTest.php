<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ActivityLog\EventSubscriber\PostActivityListLoad;

use Generator;
use Ibexa\Bundle\ActivityLog\EventSubscriber\PostActivityListLoad\UserActivity;
use Ibexa\Contracts\ActivityLog\Event\PostActivityGroupListLoadEvent;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityGroupListInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\UserService;
use PHPUnit\Framework\TestCase;
use Throwable;
use Traversable;

final class UserActivityTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Repository\UserService&\PHPUnit\Framework\MockObject\MockObject */
    private UserService $userService;

    private UserActivity $subscriber;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
        $this->subscriber = new UserActivity($this->userService);
    }

    public function testLoadingContentActivity(): void
    {
        $logGenerator = $this->createLogGenerator([
            ['user_id' => 10, 'user_set' => true],
            ['user_id' => 10, 'user_set' => true],
        ]);
        $list = $this->createList($logGenerator);

        $this->userService
            ->expects(self::once())
            ->method('loadUser')
            ->with(10);

        $this->subscriber->loadUsers(new PostActivityGroupListLoadEvent($list));
    }

    public function testSuppressingNotFoundExceptions(): void
    {
        $logGenerator = $this->createLogGenerator([
            ['user_id' => 10],
            ['user_id' => 10],
        ]);
        $list = $this->createList($logGenerator);

        $userService = $this->createMock(UserService::class);
        $userService
            ->expects(self::once())
            ->method('loadUser')
            ->with(10)
            ->willThrowException($this->createMock(NotFoundException::class));

        $subscriber = new UserActivity($userService);

        $subscriber->loadUsers(new PostActivityGroupListLoadEvent($list));
    }

    public function testOtherExceptions(): void
    {
        $logGenerator = $this->createLogGenerator([
            ['user_id' => 10],
            ['user_id' => 10],
        ]);
        $list = $this->createList($logGenerator);

        $userService = $this->createMock(UserService::class);
        $userService
            ->expects(self::once())
            ->method('loadUser')
            ->with(10)
            ->willThrowException($this->createMock(Throwable::class));

        $subscriber = new UserActivity($userService);

        $this->expectException(Throwable::class);
        $subscriber->loadUsers(new PostActivityGroupListLoadEvent($list));
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
     * @param array<array{user_id: int, user_set?: bool}> $list
     *
     * @return \Generator<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface>
     */
    private function createLogGenerator(array $list): Generator
    {
        foreach ($list as $data) {
            $group = $this->createMock(ActivityLogGroupInterface::class);

            $group->method('getUserId')
                ->willReturn($data['user_id']);

            $relatedObjectSet = $data['user_set'] ?? false;
            $group->expects($relatedObjectSet ? self::once() : self::never())
                ->method('setUser');

            yield $group;
        }
    }
}
