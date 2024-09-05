<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Contracts\Scheduler\ValueObject;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class ScheduledEntryList extends ValueObject
{
    /** @var ScheduledEntry[] */
    public $scheduledEntries = [];

    public $page;

    public $limit;

    public $total;
}

class_alias(ScheduledEntryList::class, 'EzSystems\DateBasedPublisher\API\ValueObject\ScheduledEntryList');
