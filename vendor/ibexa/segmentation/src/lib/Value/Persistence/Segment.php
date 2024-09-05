<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Segmentation\Value\Persistence;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property-read int $id
 * @property-read string $identifier
 * @property-read string $name
 * @property-read int $groupId
 *
 * @internal
 */
class Segment extends ValueObject
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $identifier;

    /** @var int */
    protected $groupId;
}

class_alias(Segment::class, 'Ibexa\Platform\Segmentation\Value\Persistence\Segment');
