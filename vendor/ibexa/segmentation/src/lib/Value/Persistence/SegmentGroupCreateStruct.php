<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Segmentation\Value\Persistence;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @internal
 */
class SegmentGroupCreateStruct extends ValueObject
{
    /** @var string */
    public $identifier;

    /** @var string */
    public $name;
}

class_alias(SegmentGroupCreateStruct::class, 'Ibexa\Platform\Segmentation\Value\Persistence\SegmentGroupCreateStruct');
