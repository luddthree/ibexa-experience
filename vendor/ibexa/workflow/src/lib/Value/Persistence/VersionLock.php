<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value\Persistence;

use Ibexa\Contracts\Core\Persistence\ValueObject;

final class VersionLock extends ValueObject
{
    /** @var int */
    public $id;

    /** @var int */
    public $contentId;

    /** @var int */
    public $version;

    /** @var int */
    public $userId;

    /** @var int */
    public $created;

    /** @var int */
    public $modified;

    /** @var bool */
    public $isLocked;
}
