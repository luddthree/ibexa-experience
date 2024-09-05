<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\EventSubscriber\PostActivityListLoad;

use Ibexa\Contracts\ActivityLog\Event\PostActivityGroupListLoadEvent;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentActivity implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ContentService $contentService;

    public function __construct(
        ContentService $contentService
    ) {
        $this->contentService = $contentService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostActivityGroupListLoadEvent::class => ['loadContent'],
        ];
    }

    /**
     * TODO: Handle lists in a more performant way.
     */
    public function loadContent(PostActivityGroupListLoadEvent $event): void
    {
        // We will store Content IDs that we have attempted to load to prevent loading the same object more than once.
        $visitedIds = [];
        $list = $event->getList();
        foreach ($list as $group) {
            foreach ($group->getActivityLogs() as $log) {
                if ($log->getObjectClass() !== Content::class) {
                    continue;
                }

                $contentId = $log->getObjectId();
                try {
                    if (!array_key_exists($contentId, $visitedIds)) {
                        $visitedIds[$contentId] = $this->loadContentById($contentId);
                    }

                    if ($visitedIds[$contentId] === null) {
                        continue;
                    }

                    $log->setRelatedObject($visitedIds[$contentId]);
                } catch (NotFoundException|UnauthorizedException $e) {
                    // Log entry contains relationship to an object that has been deleted, current user does not have
                    // access to it, or is otherwise not reachable.
                    $visitedIds[$contentId] = null;
                }
            }
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function loadContentById(string $contentId): ?Content
    {
        if (is_numeric($contentId) && $contentId == (int)$contentId) {
            return $this->contentService->loadContent((int)$contentId);
        }

        if (isset($this->logger)) {
            $this->logger->warning(sprintf(
                'Failed to load Content using ID: "%s". Content ID has to be an integerish value.',
                $contentId,
            ));
        }

        return null;
    }
}
