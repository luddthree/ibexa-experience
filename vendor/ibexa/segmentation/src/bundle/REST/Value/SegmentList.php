<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\REST\Value;

use Ibexa\Rest\Value;

final class SegmentList extends Value
{
    public SegmentGroup $segmentGroup;

    /** @var array<\Ibexa\Bundle\Segmentation\REST\Value\Segment> */
    public array $segments;

    /**
     * @param array<\Ibexa\Bundle\Segmentation\REST\Value\Segment> $segments
     */
    public function __construct(SegmentGroup $segmentGroup, array $segments)
    {
        $this->segments = $segments;
        $this->segmentGroup = $segmentGroup;
    }
}
