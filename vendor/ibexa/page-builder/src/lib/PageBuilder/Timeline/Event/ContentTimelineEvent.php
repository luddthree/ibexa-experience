<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder\Timeline\Event;

use Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ContentTimelineEvent extends Event
{
    /** @var \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextInterface */
    private $context;

    /** @var \Ibexa\Contracts\PageBuilder\Timeline\EventInterface[] */
    private $timelineEvents;

    /**
     * @param \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextInterface $context
     * @param \Ibexa\Contracts\PageBuilder\Timeline\EventInterface[] $timelineEvents
     */
    public function __construct(
        ContextInterface $context,
        array $timelineEvents = []
    ) {
        $this->context = $context;
        $this->timelineEvents = $timelineEvents;
    }

    /**
     * @return \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextInterface
     */
    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    /**
     * @param \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextInterface $context
     */
    public function setContext(ContextInterface $context): void
    {
        $this->context = $context;
    }

    /**
     * @return \Ibexa\Contracts\PageBuilder\Timeline\EventInterface[]
     */
    public function getTimelineEvents(): array
    {
        return $this->timelineEvents;
    }

    /**
     * @param \Ibexa\Contracts\PageBuilder\Timeline\EventInterface[] $timelineEvents
     */
    public function setTimelineEvents(array $timelineEvents): void
    {
        $this->timelineEvents = $timelineEvents;
    }
}

class_alias(ContentTimelineEvent::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\Event\ContentTimelineEvent');
