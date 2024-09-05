<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Segmentation\Event;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentCreateStruct;

final class CreateSegmentEvent extends AfterEvent
{
    private SegmentCreateStruct $createStruct;

    private Segment $segmentResult;

    public function __construct(SegmentCreateStruct $createStruct, Segment $segmentResult)
    {
        $this->createStruct = $createStruct;
        $this->segmentResult = $segmentResult;
    }

    public function getCreateStruct(): SegmentCreateStruct
    {
        return $this->createStruct;
    }

    public function getSegmentResult(): Segment
    {
        return $this->segmentResult;
    }
}
