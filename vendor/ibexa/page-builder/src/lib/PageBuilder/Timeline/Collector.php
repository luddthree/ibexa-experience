<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder\Timeline;

use Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextInterface;
use Ibexa\PageBuilder\PageBuilder\Timeline\Event\ContentTimelineEvent;
use Ibexa\PageBuilder\PageBuilder\Timeline\Event\TimelineEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Collector
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextInterface $context
     *
     * @return \Ibexa\Contracts\PageBuilder\Timeline\EventInterface[]
     */
    public function collect(ContextInterface $context): array
    {
        $contentTimelineEvent = new ContentTimelineEvent($context);
        $this->eventDispatcher->dispatch($contentTimelineEvent, TimelineEvents::COLLECT_EVENTS);

        return array_values($contentTimelineEvent->getTimelineEvents());
    }
}

class_alias(Collector::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\Collector');
