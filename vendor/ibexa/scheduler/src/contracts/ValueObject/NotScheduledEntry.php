<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Contracts\Scheduler\ValueObject;

class NotScheduledEntry extends ScheduledEntry
{
}

class_alias(NotScheduledEntry::class, 'EzSystems\DateBasedPublisher\API\ValueObject\NotScheduledEntry');
