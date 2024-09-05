<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\REST\Value;

use Ibexa\Rest\Value;

final class SegmentGroupList extends Value
{
    /** @var array<\Ibexa\Bundle\Segmentation\REST\Value\SegmentGroup> */
    public array $segmentGroups;

    /**
     * @param array<\Ibexa\Bundle\Segmentation\REST\Value\SegmentGroup> $segmentGroups
     */
    public function __construct(array $segmentGroups)
    {
        $this->segmentGroups = $segmentGroups;
    }
}
