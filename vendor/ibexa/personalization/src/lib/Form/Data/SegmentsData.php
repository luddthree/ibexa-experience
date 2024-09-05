<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

use Ibexa\Personalization\Value\Model\SegmentList;
use Ibexa\Personalization\Value\Model\SegmentsStruct;

final class SegmentsData extends SegmentsStruct
{
    public function setActiveSegments(SegmentList $activeSegments): void
    {
        $this->activeSegments = $activeSegments;
    }

    /**
     * @param array<\Ibexa\Personalization\Value\Model\SegmentItemGroup> $segmentItemGroups
     */
    public function setSegmentItemGroups(array $segmentItemGroups): void
    {
        $this->segmentItemGroups = $segmentItemGroups;
    }

    public static function fromSegments(SegmentsStruct $segments): self
    {
        return new self(
            $segments->getSegmentItemGroups(),
            $segments->getActiveSegments(),
            $segments->getInactiveSegments(),
            $segments->getOriginalActiveSegments(),
            $segments->getOriginalInactiveSegments()
        );
    }
}
