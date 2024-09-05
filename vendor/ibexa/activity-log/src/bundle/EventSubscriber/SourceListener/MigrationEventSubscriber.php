<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\EventSubscriber\SourceListener;

use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\Migration\Event\BeforeMigrationEvent;
use Ibexa\Contracts\Migration\Event\MigrationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MigrationEventSubscriber implements EventSubscriberInterface
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
            MigrationEvent::class => [
                'onMigrationEnd', -10,
            ],
            BeforeMigrationEvent::class => [
                'onMigrationStart', 10,
            ],
        ];
    }

    public function onMigrationStart(BeforeMigrationEvent $event): void
    {
        $this->activityLogService->prepareContext('migration', 'Migrating file: ' . $event->getMigration()->getName());
    }

    public function onMigrationEnd(MigrationEvent $event): void
    {
        $this->activityLogService->dismissContext();
    }
}
