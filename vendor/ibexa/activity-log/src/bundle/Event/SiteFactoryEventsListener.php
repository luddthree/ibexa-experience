<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Event;

use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\SiteFactory\Events\CreateSiteEvent;
use Ibexa\Contracts\SiteFactory\Events\DeleteSiteEvent;
use Ibexa\Contracts\SiteFactory\Events\UpdateSiteEvent;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SiteFactoryEventsListener implements EventSubscriberInterface
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
            CreateSiteEvent::class => ['onCreate'],
            DeleteSiteEvent::class => ['onDelete'],
            UpdateSiteEvent::class => ['onUpdate'],
        ];
    }

    public function onCreate(CreateSiteEvent $event): void
    {
        $site = $event->getSite();
        $this->saveActivityLog('create', $site->id);
    }

    public function onDelete(DeleteSiteEvent $event): void
    {
        $site = $event->getSite();
        $this->saveActivityLog('delete', $site->id);
    }

    public function onUpdate(UpdateSiteEvent $event): void
    {
        $site = $event->getSite();
        $this->saveActivityLog('update', $site->id);
    }

    private function saveActivityLog(string $action, int $id): void
    {
        $activityLog = $this->activityLogService->build(Site::class, (string)$id, $action);
        $this->activityLogService->save($activityLog);
    }
}
