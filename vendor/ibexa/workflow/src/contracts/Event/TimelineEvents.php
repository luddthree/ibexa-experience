<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Event;

final class TimelineEvents
{
    public const COLLECT_ENTRIES = 'workflow.timeline.entries.collect';

    public const ENTRY_RENDER = 'workflow.timeline.entry.render';
}

class_alias(TimelineEvents::class, 'EzSystems\EzPlatformWorkflow\Event\TimelineEvents');
