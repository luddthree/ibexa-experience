<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Segmentation\Event;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Segmentation\Value\SegmentGroup;
use Ibexa\Segmentation\Value\SegmentGroupCreateStruct;

final class CreateSegmentGroupEvent extends AfterEvent
{
    private SegmentGroupCreateStruct $createStruct;

    private SegmentGroup $segmentGroupResult;

    public function __construct(SegmentGroupCreateStruct $createStruct, SegmentGroup $segmentGroupResult)
    {
        $this->createStruct = $createStruct;
        $this->segmentGroupResult = $segmentGroupResult;
    }

    public function getCreateStruct(): SegmentGroupCreateStruct
    {
        return $this->createStruct;
    }

    public function getSegmentGroupResult(): SegmentGroup
    {
        return $this->segmentGroupResult;
    }
}
