<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\REST\Value;

use Ibexa\Rest\Value;
use Ibexa\Segmentation\Value\SegmentGroup as SegmentGroupContract;

final class SegmentGroup extends Value
{
    public SegmentGroupContract $segmentGroup;

    public function __construct(SegmentGroupContract $segmentGroup)
    {
        $this->segmentGroup = $segmentGroup;
    }
}
