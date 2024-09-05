<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Calendar;

use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;

interface ScheduledEntryBasedEvent
{
    public function getScheduledEntry(): ScheduledEntry;
}

class_alias(ScheduledEntryBasedEvent::class, 'EzSystems\DateBasedPublisher\Core\Calendar\ScheduledEntryBasedEvent');
