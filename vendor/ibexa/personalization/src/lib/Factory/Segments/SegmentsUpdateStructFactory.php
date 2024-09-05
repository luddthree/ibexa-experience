<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\Segments;

use Ibexa\Personalization\Exception\InvalidArgumentException;
use Ibexa\Personalization\Form\Data\ModelData;
use Ibexa\Personalization\Value\Model\Segment;
use Ibexa\Personalization\Value\Model\SegmentData;
use Ibexa\Personalization\Value\Model\SegmentGroup;
use Ibexa\Personalization\Value\Model\SegmentItemGroup;
use Ibexa\Personalization\Value\Model\SegmentItemGroupElement;
use Ibexa\Personalization\Value\Model\SegmentsStruct;
use Ibexa\Personalization\Value\Model\SegmentsUpdateStruct;

final class SegmentsUpdateStructFactory implements SegmentsUpdateStructFactoryInterface
{
    public function createFromModelData(ModelData $data, SegmentsStruct $segmentsStruct): SegmentsUpdateStruct
    {
        $model = $data->getModel();

        if (null === $model->getValueEventType() || null === $model->getMaximumRatingAge()) {
            throw new InvalidArgumentException('Model data is invalid');
        }

        $segments = $data->getSegments();
        $segmentItemGroups = $segments ? $segments->getSegmentItemGroups() : [];

        $allSegmentNames = $segmentsStruct->getAllSegmentsNames();
        $originalActiveSegmentsNames = $segmentsStruct->getOriginalActiveSegmentsNames();

        $filteredSegmentItemGroups = [];
        foreach ($segmentItemGroups as $itemGroup) {
            $filteredGroupElements = [];
            foreach ($itemGroup->getGroupElements() as $groupElement) {
                $mainSegment = $groupElement->getMainSegment()->getSegment();
                $mainSegmentId = $mainSegment->getId();
                $mainSegmentName = $mainSegment->getName();

                if (empty($mainSegmentName) || !in_array($mainSegmentName, $allSegmentNames, true)) {
                    continue;
                }

                $filteredChildrenSegments = $this->filterChildrenSegments(
                    $groupElement,
                    $allSegmentNames,
                    $originalActiveSegmentsNames
                );

                $filteredGroupElements[] = new SegmentItemGroupElement(
                    $groupElement->getId(),
                    $this->createSegmentData(
                        $mainSegmentId,
                        $mainSegmentName,
                        $mainSegment->getGroup()->getId(),
                        $mainSegment->getGroup()->getName(),
                        $groupElement->getMainSegment()->isActive(),
                    ),
                    $filteredChildrenSegments,
                    $groupElement->getChildGroupingOperation()
                );
            }
            $filteredSegmentItemGroups[] = new SegmentItemGroup(
                $itemGroup->getId(),
                $itemGroup->getGroupingOperation(),
                $filteredGroupElements
            );
        }

        return new SegmentsUpdateStruct(
            $filteredSegmentItemGroups,
            $segmentsStruct->getActiveSegments(),
            $model->getValueEventType(),
            $model->getMaximumRatingAge(),
        );
    }

    private function createSegmentData(
        int $id,
        string $name,
        int $groupId,
        string $groupName,
        bool $isActive
    ): SegmentData {
        return new SegmentData(
            new Segment(
                $name,
                $id,
                new SegmentGroup(
                    $groupId,
                    $groupName,
                ),
            ),
            $isActive,
        );
    }

    /**
     * @param array<string> $allSegmentNames
     * @param array<string> $originalActiveSegmentsNames
     *
     * @return array<\Ibexa\Personalization\Value\Model\SegmentData>
     */
    private function filterChildrenSegments(
        SegmentItemGroupElement $groupElement,
        array $allSegmentNames,
        array $originalActiveSegmentsNames
    ): array {
        $filteredChildrenSegments = [];

        foreach ($groupElement->getChildSegments() as $childSegment) {
            $childName = $childSegment->getSegment()->getName();
            $childId = $childSegment->getSegment()->getId();

            if (empty($childName) || !in_array($childName, $allSegmentNames, true)) {
                continue;
            }

            $filteredChildrenSegments[] = $this->createSegmentData(
                $childId,
                $childName,
                $childSegment->getSegment()->getGroup()->getId(),
                $childSegment->getSegment()->getName(),
                in_array($childName, $originalActiveSegmentsNames, true),
            );
        }

        return $filteredChildrenSegments;
    }
}
