<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property-read int $id
 * @property-read int $contentId
 * @property-read int $version
 * @property-read int $userId
 * @property-read \DateTimeImmutable $created
 * @property-read \DateTimeImmutable $modified
 * @property-read bool $isLocked
 */
final class VersionLock extends ValueObject
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $contentId;

    /** @var int */
    protected $version;

    /** @var int */
    protected $userId;

    /** @var \DateTimeImmutable */
    protected $created;

    /** @var \DateTimeImmutable */
    protected $modified;

    /** @var bool */
    protected $isLocked;
}
