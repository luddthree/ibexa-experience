<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Mapper;

use Ibexa\Personalization\Value\Model\SegmentItemGroup;

final class SegmentItemGroupMapper implements SegmentItemGroupMapperInterface
{
    private SegmentDataMapperInterface $segmentDataMapper;

    public function __construct(SegmentDataMapperInterface $segmentDataMapper)
    {
        $this->segmentDataMapper = $segmentDataMapper;
    }

    public function map(SegmentItemGroup $segmentItemGroup): array
    {
        $data = [
            'id' => $segmentItemGroup->getId(),
            'groupingOperation' => $segmentItemGroup->getGroupingOperation(),
            'groupElements' => [],
        ];

        foreach ($segmentItemGroup->getGroupElements() as $groupingElement) {
            $data['groupElements'][] = [
                'id' => $groupingElement->getId(),
                'mainSegment' => $this->segmentDataMapper->map($groupingElement->getMainSegment()),
                'childSegments' => array_map(
                    [$this->segmentDataMapper, 'map'],
                    $groupingElement->getChildSegments(),
                ),
                'childGroupingOperation' => $groupingElement->getChildGroupingOperation(),
            ];
        }

        return $data;
    }
}
