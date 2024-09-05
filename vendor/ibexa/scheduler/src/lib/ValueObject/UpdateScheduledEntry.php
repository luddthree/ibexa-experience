<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\ValueObject;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class UpdateScheduledEntry extends ValueObject
{
    public $id;

    public $userId;

    public $actionTimestamp;
}

class_alias(UpdateScheduledEntry::class, 'EzSystems\DateBasedPublisher\SPI\ValueObject\UpdateScheduledEntry');
