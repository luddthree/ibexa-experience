<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\DataTransformer;

use Ibexa\Personalization\Value\Model\Segment;
use Ibexa\Personalization\Value\Model\SegmentData;
use Ibexa\Personalization\Value\Model\SegmentGroup;
use Ibexa\Personalization\Value\Model\SegmentItemGroup;
use Ibexa\Personalization\Value\Model\SegmentItemGroupElement;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @phpstan-implements \Symfony\Component\Form\DataTransformerInterface<
 *     array<\Ibexa\Personalization\Value\Model\SegmentItemGroup>,
 *     string,
 * >
 */
final class SegmentItemGroupsTransformer implements DataTransformerInterface
{
    /**
     * @param array<\Ibexa\Personalization\Value\Model\SegmentItemGroup> $value
     */
    public function transform($value): string
    {
        $json = json_encode($value);

        if (false === $json) {
            throw new TransformationFailedException('Failed to transform value to JSON');
        }

        return $json;
    }

    /**
     * @param string $value
     *
     * @return array<\Ibexa\Personalization\Value\Model\SegmentItemGroup>
     */
    public function reverseTransform($value): array
    {
        if (empty($value)) {
            return [];
        }

        $data = json_decode($value, true);
        $segmentGroupItems = [];

        foreach ($data as $segmentGroupItem) {
            $groupElements = [];
            foreach ($segmentGroupItem['groupElements'] as $groupElement) {
                $childSegments = [];
                foreach ($groupElement['childSegments'] as $childSegment) {
                    $childSegments[] = new SegmentData(
                        new Segment(
                            $childSegment['segment']['name'],
                            (int) $childSegment['segment']['id'],
                            new SegmentGroup(
                                (int) $childSegment['segment']['group']['id'],
                                $childSegment['segment']['group']['name'],
                            ),
                        ),
                        $childSegment['isActive'] ?? true
                    );
                }

                $groupElements[] = new SegmentItemGroupElement(
                    0,
                    new SegmentData(
                        new Segment(
                            $groupElement['mainSegment']['segment']['name'],
                            (int) $groupElement['mainSegment']['segment']['id'],
                            new SegmentGroup(
                                (int) $groupElement['mainSegment']['segment']['group']['id'],
                                $groupElement['mainSegment']['segment']['group']['name'],
                            ),
                        ),
                        $groupElement['mainSegment']['isActive'] ?? false,
                    ),
                    $childSegments,
                    $groupElement['childGroupingOperation'],
                );
            }
            $segmentGroupItems[] = new SegmentItemGroup(
                0,
                $segmentGroupItem['groupingOperation'],
                $groupElements,
            );
        }

        return $segmentGroupItems;
    }
}
