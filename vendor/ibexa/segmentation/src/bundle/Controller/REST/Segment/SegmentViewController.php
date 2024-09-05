<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Controller\REST\Segment;

use Ibexa\Bundle\Segmentation\REST\Value\Segment;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Rest\Server\Controller as RestController;

final class SegmentViewController extends RestController
{
    private SegmentationServiceInterface $segmentationService;

    public function __construct(SegmentationServiceInterface $segmentationService)
    {
        $this->segmentationService = $segmentationService;
    }

    public function createView(string $identifier): Segment
    {
        $segment = $this->segmentationService->loadSegmentByIdentifier($identifier);

        return new Segment($segment);
    }
}
