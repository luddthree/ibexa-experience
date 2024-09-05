<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder\Timeline\Event;

final class TimelineEvents
{
    /**
     * Event triggered on collecting Content timeline events.
     *
     * Dispatcher passes @see \Ibexa\PageBuilder\PageBuilder\Timeline\Event\ContentTimelineEvent.
     *
     * @var string
     */
    public const COLLECT_EVENTS = 'ezplatform.page_builder.timeline.collect_events';
}

class_alias(TimelineEvents::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\Event\TimelineEvents');
