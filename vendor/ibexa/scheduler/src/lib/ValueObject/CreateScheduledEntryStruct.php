<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\ValueObject;

class CreateScheduledEntryStruct
{
    public $userId;

    public $contentId;

    public $versionId;

    public $versionNumber;

    public $actionTimestamp;

    public $action;

    public $urlRoot;
}

class_alias(CreateScheduledEntryStruct::class, 'EzSystems\DateBasedPublisher\SPI\ValueObject\CreateScheduledEntryStruct');
