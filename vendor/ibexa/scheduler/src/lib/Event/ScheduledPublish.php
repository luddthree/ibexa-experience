<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Event;

use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;
use Symfony\Contracts\EventDispatcher\Event;

final class ScheduledPublish extends Event
{
    /** @var \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry */
    private $scheduledEntry;

    public function __construct(
        ScheduledEntry $scheduledEntry
    ) {
        $this->scheduledEntry = $scheduledEntry;
    }

    public function getScheduledEntry(): ScheduledEntry
    {
        return $this->scheduledEntry;
    }
}
