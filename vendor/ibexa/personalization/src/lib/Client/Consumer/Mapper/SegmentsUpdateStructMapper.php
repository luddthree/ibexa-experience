<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Mapper;

use Ibexa\Personalization\Value\Model\SegmentsUpdateStruct;

final class SegmentsUpdateStructMapper implements SegmentsUpdateStructMapperInterface
{
    private SegmentListMapperInterface $segmentListMapper;

    private SegmentItemGroupMapperInterface $segmentItemGroupMapper;

    public function __construct(
        SegmentListMapperInterface $segmentListMapper,
        SegmentItemGroupMapperInterface $segmentItemGroupMapper
    ) {
        $this->segmentListMapper = $segmentListMapper;
        $this->segmentItemGroupMapper = $segmentItemGroupMapper;
    }

    public function map(SegmentsUpdateStruct $segmentsUpdateStruct): array
    {
        return [
            'segmentItemGroups' => array_map(
                [$this->segmentItemGroupMapper, 'map'],
                $segmentsUpdateStruct->getSegmentItemGroups()
            ),
            'activeSegments' => $this->segmentListMapper->map(
                $segmentsUpdateStruct->getActiveSegments()
            ),
            'eventType' => $segmentsUpdateStruct->getEventType(),
            'maximumRatingAge' => $segmentsUpdateStruct->getMaximumRatingAge(),
        ];
    }
}
