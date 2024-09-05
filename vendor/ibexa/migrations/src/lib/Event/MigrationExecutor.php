<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Event;

use Ibexa\Contracts\Migration\Event\BeforeMigrationEvent;
use Ibexa\Contracts\Migration\Event\MigrationEvent;
use Ibexa\Contracts\Migration\MigrationExecutor as MigrationExecutorInterface;
use Ibexa\Migration\Repository\Migration;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class MigrationExecutor implements MigrationExecutorInterface
{
    private MigrationExecutorInterface $innerService;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        MigrationExecutorInterface $inner,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->innerService = $inner;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute(Migration $migration): void
    {
        $beforeEvent = new BeforeMigrationEvent($migration);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->execute($migration);

        $this->eventDispatcher->dispatch(
            new MigrationEvent($migration)
        );
    }
}
