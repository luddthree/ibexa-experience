<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Data;

use Ibexa\Segmentation\Value\SegmentGroup;

final class SegmentGroupDeleteData
{
    /** @var \Ibexa\Segmentation\Value\SegmentGroup|null */
    private $segmentGroup;

    public function __construct(?SegmentGroup $segmentGroup = null)
    {
        $this->segmentGroup = $segmentGroup;
    }

    public function getSegmentGroup(): ?SegmentGroup
    {
        return $this->segmentGroup;
    }

    public function setSegmentGroup(SegmentGroup $segmentGroup): void
    {
        $this->segmentGroup = $segmentGroup;
    }
}

class_alias(SegmentGroupDeleteData::class, 'Ibexa\Platform\Segmentation\Data\SegmentGroupDeleteData');
