<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Segmentation\Event;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Segmentation\Value\SegmentGroup;
use Ibexa\Segmentation\Value\SegmentGroupUpdateStruct;

final class UpdateSegmentGroupEvent extends AfterEvent
{
    private SegmentGroup $segmentGroup;

    private SegmentGroupUpdateStruct $updateStruct;

    private SegmentGroup $segmentGroupResult;

    public function __construct(
        SegmentGroup $segmentGroup,
        SegmentGroupUpdateStruct $updateStruct,
        SegmentGroup $segmentGroupResult
    ) {
        $this->segmentGroup = $segmentGroup;
        $this->updateStruct = $updateStruct;
        $this->segmentGroupResult = $segmentGroupResult;
    }

    public function getSegmentGroup(): SegmentGroup
    {
        return $this->segmentGroup;
    }

    public function getUpdateStruct(): SegmentGroupUpdateStruct
    {
        return $this->updateStruct;
    }

    public function getSegmentGroupResult(): SegmentGroup
    {
        return $this->segmentGroupResult;
    }
}
