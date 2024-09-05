<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

class SegmentsStruct
{
    /** @var array<\Ibexa\Personalization\Value\Model\SegmentItemGroup> */
    protected array $segmentItemGroups;

    protected SegmentList $activeSegments;

    protected SegmentList $inactiveSegments;

    protected SegmentList $originalActiveSegments;

    protected SegmentList $originalInactiveSegments;

    /**
     * @param array<\Ibexa\Personalization\Value\Model\SegmentItemGroup> $segmentItemGroups
     */
    public function __construct(
        array $segmentItemGroups,
        SegmentList $activeSegments,
        SegmentList $inactiveSegments,
        SegmentList $originalActiveSegments,
        SegmentList $originalInactiveSegments
    ) {
        $this->segmentItemGroups = $segmentItemGroups;
        $this->activeSegments = $activeSegments;
        $this->inactiveSegments = $inactiveSegments;
        $this->originalActiveSegments = $originalActiveSegments;
        $this->originalInactiveSegments = $originalInactiveSegments;
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Model\SegmentItemGroup>
     */
    public function getSegmentItemGroups(): array
    {
        return $this->segmentItemGroups;
    }

    public function getActiveSegments(): SegmentList
    {
        return $this->activeSegments;
    }

    public function getInactiveSegments(): SegmentList
    {
        return $this->inactiveSegments;
    }

    public function getOriginalActiveSegments(): SegmentList
    {
        return $this->originalActiveSegments;
    }

    public function getOriginalInactiveSegments(): SegmentList
    {
        return $this->originalInactiveSegments;
    }

    /**
     * @return array<string>
     */
    public function getAllSegmentsNames(): array
    {
        return [
            ...$this->getOriginalActiveSegmentsNames(),
            ...$this->getOriginalInactiveSegmentsNames(),
        ];
    }

    /**
     * @return array<string>
     */
    public function getOriginalActiveSegmentsNames(): array
    {
        return array_map(
            static fn (Segment $segment): string => $segment->getName(),
            [...$this->getOriginalActiveSegments()]
        );
    }

    /**
     * @return array<string>
     */
    public function getOriginalInactiveSegmentsNames(): array
    {
        return array_map(
            static fn (Segment $segment): string => $segment->getName(),
            [...$this->getOriginalInactiveSegments()]
        );
    }
}
