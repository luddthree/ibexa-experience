<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\EventSubscriber\PostActivityListLoad;

use Ibexa\Contracts\ActivityLog\Event\PostActivityGroupListLoadEvent;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\UserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserActivity implements EventSubscriberInterface
{
    private UserService $userService;

    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostActivityGroupListLoadEvent::class => ['loadUsers'],
        ];
    }

    /**
     * TODO: Handle lists in a more performant way.
     */
    public function loadUsers(PostActivityGroupListLoadEvent $event): void
    {
        // We will store User IDs that we have attempted to load to prevent loading the same object more than once.
        $visitedIds = [];
        $list = $event->getList();
        foreach ($list as $log) {
            $userId = $log->getUserId();
            try {
                if (!array_key_exists($userId, $visitedIds)) {
                    $visitedIds[$userId] = $this->userService->loadUser($userId);
                }

                if ($visitedIds[$userId] === null) {
                    continue;
                }

                $log->setUser($visitedIds[$userId]);
            } catch (NotFoundException $e) {
                // TODO: Check how to perform authorization checks
                // Log entries that have inaccessible components are expected
                $visitedIds[$userId] = null;
            }
        }
    }
}
