<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Controller\REST\Segment;

use Ibexa\Bundle\Segmentation\REST\Value\Segment as RestSegment;
use Ibexa\Bundle\Segmentation\REST\Value\SegmentGroup;
use Ibexa\Bundle\Segmentation\REST\Value\SegmentList;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Segmentation\Value\Segment;

final class SegmentListController extends RestController
{
    private SegmentationServiceInterface $segmentationService;

    public function __construct(SegmentationServiceInterface $segmentationService)
    {
        $this->segmentationService = $segmentationService;
    }

    public function createView(string $identifier): SegmentList
    {
        $segmentGroup = $this->segmentationService->loadSegmentGroupByIdentifier($identifier);
        $segments = $this->segmentationService->loadSegmentsAssignedToGroup($segmentGroup);

        $restSegments = array_map(
            static fn (Segment $segmentGroup): RestSegment => new RestSegment($segmentGroup),
            $segments
        );

        return new SegmentList(
            new SegmentGroup($segmentGroup),
            $restSegments,
        );
    }
}
