<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Contracts\Scheduler\ValueObject;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class ScheduledEntry extends ValueObject
{
    public $id;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    public $user;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo|null */
    public $versionInfo;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    public $content;

    /** @var \DateTime */
    public $date;

    /** @var string */
    public $urlRoot;

    /** @var string */
    public $action;
}

class_alias(ScheduledEntry::class, 'EzSystems\DateBasedPublisher\API\ValueObject\ScheduledEntry');
