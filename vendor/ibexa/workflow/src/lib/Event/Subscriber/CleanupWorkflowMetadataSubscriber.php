<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CleanupWorkflowMetadataSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface */
    private $handler;

    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PublishVersionEvent::class => ['onPublishVersion'],
        ];
    }

    public function onPublishVersion(PublishVersionEvent $event): void
    {
        $this->handler->cleanupWorkflowMetadataForContent($event->getVersionInfo()->contentInfo->id);
    }
}
