<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Segmentation\Event;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentCreateStruct;
use UnexpectedValueException;

final class BeforeCreateSegmentEvent extends BeforeEvent
{
    private SegmentCreateStruct $createStruct;

    private ?Segment $segmentResult;

    public function __construct(SegmentCreateStruct $createStruct, ?Segment $segmentResult = null)
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
