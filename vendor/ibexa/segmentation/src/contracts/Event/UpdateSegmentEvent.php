<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Segmentation\Event;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentUpdateStruct;

final class UpdateSegmentEvent extends AfterEvent
{
    private Segment $segment;

    private SegmentUpdateStruct $updateStruct;

    private Segment $segmentResult;

    public function __construct(Segment $segment, SegmentUpdateStruct $updateStruct, Segment $segmentResult)
    {
        $this->segment = $segment;
        $this->updateStruct = $updateStruct;
        $this->segmentResult = $segmentResult;
    }

    public function getSegment(): Segment
    {
        return $this->segment;
    }

    public function getUpdateStruct(): SegmentUpdateStruct
    {
        return $this->updateStruct;
    }

    public function getSegmentResult(): Segment
    {
        return $this->segmentResult;
    }
}
