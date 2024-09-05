<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Segmentation\Event;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Segmentation\Value\SegmentGroup;

final class RemoveSegmentGroupEvent extends AfterEvent
{
    private SegmentGroup $segmentGroup;

    public function __construct(SegmentGroup $segmentGroup)
    {
        $this->segmentGroup = $segmentGroup;
    }

    public function getSegmentGroup(): SegmentGroup
    {
        return $this->segmentGroup;
    }
}
