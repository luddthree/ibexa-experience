<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Segmentation\Event;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Segmentation\Value\SegmentGroup;
use Ibexa\Segmentation\Value\SegmentGroupUpdateStruct;
use UnexpectedValueException;

final class BeforeUpdateSegmentGroupEvent extends BeforeEvent
{
    private SegmentGroup $segmentGroup;

    private SegmentGroupUpdateStruct $updateStruct;

    private ?SegmentGroup $segmentGroupResult;

    public function __construct(
        SegmentGroup $segmentGroup,
        SegmentGroupUpdateStruct $updateStruct,
        ?SegmentGroup $segmentGroupResult = null
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
        if ($this->segmentGroupResult === null) {
            $message = 'Return value is not set or not of type %s. Check hasSegmentGroupResult() or set it using setSegmentGroupResult() before you call the getter.';

            throw new UnexpectedValueException(sprintf($message, SegmentGroup::class));
        }

        return $this->segmentGroupResult;
    }

    public function setSegmentGroupResult(?SegmentGroup $segmentGroupResult): void
    {
        $this->segmentGroupResult = $segmentGroupResult;
    }

    public function hasSegmentGroupResult(): bool
    {
        return $this->segmentGroupResult !== null;
    }
}
