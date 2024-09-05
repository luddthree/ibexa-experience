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
 *
 * @internal
 */
class SegmentGroup extends ValueObject
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $identifier;

    /** @var string */
    protected $name;
}

class_alias(SegmentGroup::class, 'Ibexa\Platform\Segmentation\Value\Persistence\SegmentGroup');
