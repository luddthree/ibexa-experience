<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\ContextProvider;

use FOS\HttpCache\UserContext\ContextProvider;
use FOS\HttpCache\UserContext\UserContext;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;

/**
 * @internal
 */
final class SegmentationContextProvider implements ContextProvider
{
    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface */
    private $segmentationService;

    public function __construct(SegmentationServiceInterface $segmentationService)
    {
        $this->segmentationService = $segmentationService;
    }

    public function updateUserContext(UserContext $context): void
    {
        $segments = $this->segmentationService->loadSegmentsAssignedToCurrentUser();
        $segmentIds = array_column($segments, 'id');

        sort($segmentIds);

        $context->addParameter('segments', $segmentIds);
    }
}

class_alias(SegmentationContextProvider::class, 'Ibexa\Platform\Segmentation\ContextProvider\SegmentationContextProvider');
