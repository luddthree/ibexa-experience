<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\Segments;

use Ibexa\Personalization\Mapper\SegmentMapperInterface;
use Ibexa\Personalization\Value\Model\Segment;
use Ibexa\Personalization\Value\Model\SegmentData;
use Ibexa\Personalization\Value\Model\SegmentGroup;
use Ibexa\Personalization\Value\Model\SegmentItemGroup;
use Ibexa\Personalization\Value\Model\SegmentItemGroupElement;
use Ibexa\Personalization\Value\Model\SegmentList;
use Ibexa\Personalization\Value\Model\SegmentsStruct;
use Ibexa\Segmentation\Value\Segment as ApiSegment;

/**
 * @phpstan-import-type T from \Ibexa\Personalization\Factory\Segments\SegmentsStructFactoryInterface as TResponseContents
 */
final class SegmentsStructFactory implements SegmentsStructFactoryInterface
{
    public const PARAM_SEGMENT_ITEM_GROUPS = 'segmentItemGroups';

    public const PARAM_ACTIVE_SEGMENT = 'activeSegments';

    private SegmentMapperInterface $segmentMapper;

    public function __construct(SegmentMapperInterface $segmentMapper)
    {
        $this->segmentMapper = $segmentMapper;
    }

    public function createSegmentsStruct(array $responseContents): SegmentsStruct
    {
        $segmentsNames = $this->getSegmentsNames($responseContents);
        $segmentsMapping = $this->segmentMapper->getMapping($segmentsNames);

        $originalActiveSegments = $this->segmentMapper->mapSegments(
            $responseContents[self::PARAM_ACTIVE_SEGMENT],
            $segmentsMapping
        );

        $segmentItemGroups = [];
        $inactiveSegmentIds = [];

        foreach ($responseContents[self::PARAM_SEGMENT_ITEM_GROUPS] as $segmentItemGroup) {
            $groupElements = [];

            foreach ($segmentItemGroup['groupElements'] as $groupElement) {
                $mainSegment = $groupElement['mainSegment'];
                $segmentId = (int) $mainSegment['segment'];
                $apiSegment = $this->segmentMapper->mapSegment($segmentId, $segmentsMapping);

                if (null === $apiSegment) {
                    continue;
                }

                if (!in_array($segmentId, $responseContents[self::PARAM_ACTIVE_SEGMENT])) {
                    $inactiveSegmentIds[] = $segmentId;
                }

                $activeSegmentKey = array_search(
                    $segmentId,
                    $responseContents[self::PARAM_ACTIVE_SEGMENT],
                    true
                );

                if (false !== $activeSegmentKey) {
                    unset($responseContents[self::PARAM_ACTIVE_SEGMENT][$activeSegmentKey]);
                }

                $childSegments = $this->processChildSegments(
                    $groupElement['childSegments'],
                    $segmentsMapping,
                );

                $inactiveSegmentIds = array_merge(
                    $inactiveSegmentIds,
                    $this->getInactiveSegmentIds(
                        array_map(
                            static fn (SegmentData $segmentData): int => $segmentData->getSegment()->getId(),
                            $childSegments
                        ),
                        $responseContents[self::PARAM_ACTIVE_SEGMENT],
                    )
                );

                $groupElements[] = new SegmentItemGroupElement(
                    $groupElement['id'],
                    $this->createSegmentDataFromApiObject($apiSegment, $mainSegment['isActive']),
                    $childSegments,
                    $groupElement['childGroupingOperation']
                );
            }

            if (empty($groupElements)) {
                continue;
            }

            $segmentItemGroups[] = new SegmentItemGroup(
                $segmentItemGroup['id'],
                $segmentItemGroup['groupingOperation'],
                $groupElements
            );
        }

        $activeSegments = $this->segmentMapper->mapSegments(
            $responseContents[self::PARAM_ACTIVE_SEGMENT],
            $segmentsMapping
        );

        $mapInactiveSegments = $this->segmentMapper->mapSegments(
            $inactiveSegmentIds,
            $segmentsMapping
        );

        return new SegmentsStruct(
            $segmentItemGroups,
            SegmentList::fromArray($activeSegments),
            SegmentList::fromArray($mapInactiveSegments),
            SegmentList::fromArray($originalActiveSegments),
            SegmentList::fromArray($mapInactiveSegments)
        );
    }

    /**
     * @phpstan-param TResponseContents $responseContents
     *
     * @return array<string>
     */
    private function getSegmentsNames(array $responseContents): array
    {
        $segments = $responseContents[self::PARAM_ACTIVE_SEGMENT];

        foreach ($responseContents[self::PARAM_SEGMENT_ITEM_GROUPS] as $segmentItemsInGroup) {
            foreach ($segmentItemsInGroup['groupElements'] as $groupElement) {
                $segments[] = $groupElement['mainSegment']['segment'];
                foreach ($groupElement['childSegments'] as $childSegment) {
                    $segments[] = $childSegment['segment'];
                }
            }
        }

        return array_unique($segments);
    }

    private function createSegmentDataFromApiObject(
        ApiSegment $segment,
        bool $isActive
    ): SegmentData {
        return new SegmentData(
            new Segment(
                $segment->name,
                $segment->id,
                new SegmentGroup(
                    $segment->group->id,
                    $segment->group->name
                ),
            ),
            $isActive,
        );
    }

    /**
     * @param array<array{segment: string, isActive: bool}> $childSegments
     * @param array<int, \Ibexa\Segmentation\Value\Segment> $segmentsMapping
     *
     * @return array<\Ibexa\Personalization\Value\Model\SegmentData>
     */
    private function processChildSegments(
        array $childSegments,
        array $segmentsMapping
    ): array {
        $processedChildSegments = [];
        foreach ($childSegments as $childSegment) {
            $apiChildSegment = $this->segmentMapper->mapSegment(
                (int)$childSegment['segment'],
                $segmentsMapping
            );

            if (null === $apiChildSegment) {
                continue;
            }

            $processedChildSegments[] = $this->createSegmentDataFromApiObject(
                $apiChildSegment,
                $childSegment['isActive'],
            );
        }

        return $processedChildSegments;
    }

    /**
     * @param array<int> $segments
     * @param array<string> $activeSegments
     *
     * @return array<int>
     */
    private function getInactiveSegmentIds(array $segments, array $activeSegments): array
    {
        return array_filter(
            $segments,
            static fn (int $segmentId): bool => !in_array($segmentId, $activeSegments)
        );
    }
}
