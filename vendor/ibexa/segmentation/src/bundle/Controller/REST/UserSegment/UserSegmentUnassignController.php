<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Controller\REST\UserSegment;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Values\NoContent;

final class UserSegmentUnassignController extends RestController
{
    private SegmentationServiceInterface $segmentationService;

    private UserService $userService;

    public function __construct(SegmentationServiceInterface $segmentationService, UserService $userService)
    {
        $this->segmentationService = $segmentationService;
        $this->userService = $userService;
    }

    public function removeSegmentFromUser(int $userId, string $segmentIdentifier): NoContent
    {
        $user = $this->userService->loadUser($userId);
        $segment = $this->segmentationService->loadSegmentByIdentifier($segmentIdentifier);
        $this->segmentationService->unassignUserFromSegment($user, $segment);

        return new NoContent();
    }
}
