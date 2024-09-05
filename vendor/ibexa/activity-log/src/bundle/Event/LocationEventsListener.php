<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Event;

use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\Core\Repository\Events\Location\CopySubtreeEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\CreateLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\DeleteLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\HideLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\MoveSubtreeEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\SwapLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\UnhideLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\UpdateLocationEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class LocationEventsListener implements EventSubscriberInterface
{
    private ActivityLogServiceInterface $activityLogService;

    public function __construct(
        ActivityLogServiceInterface $activityLogService
    ) {
        $this->activityLogService = $activityLogService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CopySubtreeEvent::class => ['onCopy'],
            CreateLocationEvent::class => ['onCreate'],
            DeleteLocationEvent::class => ['onDelete'],
            HideLocationEvent::class => ['onHide'],
            MoveSubtreeEvent::class => ['onMove'],
            SwapLocationEvent::class => ['onSwap'],
            UnhideLocationEvent::class => ['onUnhide'],
            UpdateLocationEvent::class => ['onUpdate'],
        ];
    }

    public function onCopy(CopySubtreeEvent $event): void
    {
        $location = $event->getLocation();
        $this->saveActivityLog('copy', $location->id);
    }

    public function onCreate(CreateLocationEvent $event): void
    {
        $location = $event->getLocation();
        $this->saveActivityLog('create', $location->id);
    }

    public function onDelete(DeleteLocationEvent $event): void
    {
        $location = $event->getLocation();
        $this->saveActivityLog('delete', $location->id);
    }

    public function onHide(HideLocationEvent $event): void
    {
        $location = $event->getLocation();
        $this->saveActivityLog('hide', $location->id);
    }

    public function onMove(MoveSubtreeEvent $event): void
    {
        $location = $event->getLocation();
        $this->saveActivityLog('move', $location->id);
    }

    public function onSwap(SwapLocationEvent $event): void
    {
        $location = $event->getLocation1();
        $this->saveActivityLog('swap', $location->id);

        $location = $event->getLocation2();
        $this->saveActivityLog('swap', $location->id);
    }

    public function onUnhide(UnhideLocationEvent $event): void
    {
        $location = $event->getLocation();
        $this->saveActivityLog('reveal', $location->id);
    }

    public function onUpdate(UpdateLocationEvent $event): void
    {
        $location = $event->getLocation();
        $this->saveActivityLog('update', $location->id);
    }

    private function saveActivityLog(string $action, int $id): void
    {
        $activityLog = $this->activityLogService->build(Location::class, (string)$id, $action);
        $this->activityLogService->save($activityLog);
    }
}
