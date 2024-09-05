<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Controller\REST\SegmentGroup;

use Ibexa\Bundle\Segmentation\REST\Value\SegmentGroup as RestSegmentGroup;
use Ibexa\Bundle\Segmentation\REST\Value\SegmentGroupList;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Segmentation\Value\SegmentGroup;

final class SegmentGroupListController extends RestController
{
    private SegmentationServiceInterface $segmentationService;

    public function __construct(SegmentationServiceInterface $segmentationService)
    {
        $this->segmentationService = $segmentationService;
    }

    public function createView(): SegmentGroupList
    {
        $segmentGroups = $this->segmentationService->loadSegmentGroups();

        return new SegmentGroupList(
            array_map(static function (SegmentGroup $segmentGroup): RestSegmentGroup {
                return new RestSegmentGroup($segmentGroup);
            }, $segmentGroups)
        );
    }
}
