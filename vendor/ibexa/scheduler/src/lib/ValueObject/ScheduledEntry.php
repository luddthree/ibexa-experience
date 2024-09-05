<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\ValueObject;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class ScheduledEntry extends ValueObject
{
    public $id;

    public $userId;

    public $contentId;

    public $versionId;

    public $versionNumber;

    public $actionTimestamp;

    public $urlRoot;

    public $action;
}

class_alias(ScheduledEntry::class, 'EzSystems\DateBasedPublisher\SPI\ValueObject\ScheduledEntry');
