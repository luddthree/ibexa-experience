<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

use JsonSerializable;

final class SegmentItemGroupElement implements JsonSerializable
{
    private int $id;

    private SegmentData $mainSegment;

    /** @var array<\Ibexa\Personalization\Value\Model\SegmentData> */
    private array $childSegments;

    /** @phpstan-var \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::* */
    private string $childGroupingOperation;

    /**
     * @param array<\Ibexa\Personalization\Value\Model\SegmentData> $childSegments
     * @phpstan-param \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::* $childGroupingOperation
     */
    public function __construct(
        int $id,
        SegmentData $mainSegment,
        array $childSegments,
        string $childGroupingOperation
    ) {
        $this->id = $id;
        $this->mainSegment = $mainSegment;
        $this->childSegments = $childSegments;
        $this->childGroupingOperation = $childGroupingOperation;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getMainSegment(): SegmentData
    {
        return $this->mainSegment;
    }

    public function setMainSegment(SegmentData $mainSegment): void
    {
        $this->mainSegment = $mainSegment;
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Model\SegmentData>
     */
    public function getChildSegments(): array
    {
        return $this->childSegments;
    }

    /**
     * @param array<\Ibexa\Personalization\Value\Model\SegmentData> $childSegments
     */
    public function setChildSegments(array $childSegments): void
    {
        $this->childSegments = $childSegments;
    }

    /**
     * @phpstan-return \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*
     */
    public function getChildGroupingOperation(): string
    {
        return $this->childGroupingOperation;
    }

    /**
     * @phpstan-param \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::* $childGroupingOperation
     */
    public function setChildGroupingOperation(string $childGroupingOperation): void
    {
        $this->childGroupingOperation = $childGroupingOperation;
    }

    /**
     * @phpstan-return array{
     *      'id': int,
     *      'mainSegment': \Ibexa\Personalization\Value\Model\SegmentData,
     *      'childSegments': array<\Ibexa\Personalization\Value\Model\SegmentData>,
     *      'childGroupingOperation': \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'mainSegment' => $this->mainSegment,
            'childSegments' => $this->childSegments,
            'childGroupingOperation' => $this->childGroupingOperation,
        ];
    }
}
