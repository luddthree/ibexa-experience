<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Segmentation\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Segmentation\Validator\Constraints\UniqueSegmentGroupIdentifier;
use Symfony\Component\Validator\Constraints as Assert;

class SegmentGroupCreateStruct extends ValueObject
{
    /**
     * @var string
     *
     * @UniqueSegmentGroupIdentifier
     */
    public $identifier;

    /** @var string */
    public $name;

    /**
     * @var \Ibexa\Segmentation\Value\SegmentCreateStruct[]
     *
     * @Assert\Valid()
     */
    public $createSegments = [];
}

class_alias(SegmentGroupCreateStruct::class, 'Ibexa\Platform\Segmentation\Value\SegmentGroupCreateStruct');
