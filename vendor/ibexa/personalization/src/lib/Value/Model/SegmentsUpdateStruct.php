<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

use JsonSerializable;

final class SegmentsUpdateStruct implements JsonSerializable
{
    /** @var array<\Ibexa\Personalization\Value\Model\SegmentItemGroup> */
    private array $segmentItemGroups;

    private string $eventType;

    private string $maximumRatingAge;

    private SegmentList $activeSegments;

    /**
     * @param array<\Ibexa\Personalization\Value\Model\SegmentItemGroup> $segmentItemGroups
     */
    public function __construct(
        array $segmentItemGroups,
        SegmentList $activeSegments,
        string $eventType,
        string $maximumRatingAge
    ) {
        $this->segmentItemGroups = $segmentItemGroups;
        $this->eventType = $eventType;
        $this->maximumRatingAge = $maximumRatingAge;
        $this->activeSegments = $activeSegments;
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Model\SegmentItemGroup>
     */
    public function getSegmentItemGroups(): array
    {
        return $this->segmentItemGroups;
    }

    /**
     * @param array<\Ibexa\Personalization\Value\Model\SegmentItemGroup> $segmentItemGroups
     */
    public function setSegmentItemGroups(array $segmentItemGroups): void
    {
        $this->segmentItemGroups = $segmentItemGroups;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): void
    {
        $this->eventType = $eventType;
    }

    public function getMaximumRatingAge(): string
    {
        return $this->maximumRatingAge;
    }

    public function setMaximumRatingAge(string $maximumRatingAge): void
    {
        $this->maximumRatingAge = $maximumRatingAge;
    }

    public function getActiveSegments(): SegmentList
    {
        return $this->activeSegments;
    }

    /**
     * @return array{
     *      activeSegments: SegmentList,
     *      segmentItemGroups: array<\Ibexa\Personalization\Value\Model\SegmentItemGroup>,
     *      eventType: string,
     *      maximumRatingAge: string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'activeSegments' => $this->getActiveSegments(),
            'segmentItemGroups' => $this->getSegmentItemGroups(),
            'eventType' => $this->getEventType(),
            'maximumRatingAge' => $this->getMaximumRatingAge(),
        ];
    }
}
