<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Segmentation\Event;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentUpdateStruct;
use UnexpectedValueException;

final class BeforeUpdateSegmentEvent extends BeforeEvent
{
    private Segment $segment;

    private SegmentUpdateStruct $updateStruct;

    private ?Segment $segmentResult;

    public function __construct(Segment $segment, SegmentUpdateStruct $updateStruct, ?Segment $segmentResult = null)
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
        if ($this->segmentResult === null) {
            $message = 'Return value is not set or not of type %s. Check hasSegmentResult() or set it using setSegmentResult() before you call the getter.';

            throw new UnexpectedValueException(sprintf($message, Segment::class));
        }

        return $this->segmentResult;
    }

    public function setSegmentResult(?Segment $segmentResult): void
    {
        $this->segmentResult = $segmentResult;
    }

    public function hasSegmentResult(): bool
    {
        return $this->segmentResult !== null;
    }
}
