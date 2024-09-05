<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Segmentation\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class SegmentUpdateStruct extends ValueObject
{
    /** @var string */
    public $identifier;

    /** @var string */
    public $name;

    /** @var \Ibexa\Segmentation\Value\SegmentGroup */
    public $group;
}

class_alias(SegmentUpdateStruct::class, 'Ibexa\Platform\Segmentation\Value\SegmentUpdateStruct');
