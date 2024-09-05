<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Controller\REST\UserSegment;

use Ibexa\Bundle\Segmentation\REST\Value\UserSegment;
use Ibexa\Bundle\Segmentation\REST\Value\UserSegmentsList;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Segmentation\Value\Segment;

final class UserSegmentListController extends RestController
{
    private SegmentationServiceInterface $segmentationService;

    private UserService $userService;

    public function __construct(SegmentationServiceInterface $segmentationService, UserService $userService)
    {
        $this->segmentationService = $segmentationService;
        $this->userService = $userService;
    }

    public function createView(int $userId): UserSegmentsList
    {
        $user = $this->userService->loadUser($userId);
        $segments = $this->segmentationService->loadSegmentsAssignedToUser($user);

        $userSegments = array_map(
            static fn (Segment $segment): UserSegment => new UserSegment($user, $segment),
            $segments
        );

        return new UserSegmentsList($user, $userSegments);
    }
}
