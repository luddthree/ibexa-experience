<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Controller\REST\SegmentGroup;

use Ibexa\Bundle\Segmentation\REST\Value\SegmentGroup;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Rest\Server\Controller as RestController;

final class SegmentGroupViewController extends RestController
{
    private SegmentationServiceInterface $segmentationService;

    public function __construct(SegmentationServiceInterface $segmentationService)
    {
        $this->segmentationService = $segmentationService;
    }

    public function createView(string $identifier): SegmentGroup
    {
        $segment = $this->segmentationService->loadSegmentGroupByIdentifier($identifier);

        return new SegmentGroup($segment);
    }
}
