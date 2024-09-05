<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Segmentation;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroup;
use Ibexa\Segmentation\Value\SegmentGroupCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroupUpdateStruct;
use Ibexa\Segmentation\Value\SegmentUpdateStruct;

abstract class SegmentationServiceDecorator implements SegmentationServiceInterface
{
    protected SegmentationServiceInterface $innerService;

    public function __construct(SegmentationServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function loadSegment(int $segmentId): Segment
    {
        return $this->innerService->loadSegment($segmentId);
    }

    public function loadSegmentByIdentifier(string $identifier): Segment
    {
        return $this->innerService->loadSegmentByIdentifier($identifier);
    }

    public function createSegment(SegmentCreateStruct $createStruct): Segment
    {
        return $this->innerService->createSegment($createStruct);
    }

    public function updateSegment($segment, SegmentUpdateStruct $updateStruct): Segment
    {
        return $this->innerService->updateSegment($segment, $updateStruct);
    }

    public function removeSegment(Segment $segment): void
    {
        $this->innerService->removeSegment($segment);
    }

    public function loadSegmentsAssignedToGroup(SegmentGroup $segmentGroup): array
    {
        return $this->innerService->loadSegmentsAssignedToGroup($segmentGroup);
    }

    public function loadSegmentGroup(int $segmentGroupId): SegmentGroup
    {
        return $this->innerService->loadSegmentGroup($segmentGroupId);
    }

    public function loadSegmentGroupByIdentifier(string $identifier): SegmentGroup
    {
        return $this->innerService->loadSegmentGroupByIdentifier($identifier);
    }

    public function createSegmentGroup(SegmentGroupCreateStruct $createStruct): SegmentGroup
    {
        return $this->innerService->createSegmentGroup($createStruct);
    }

    public function updateSegmentGroup($segmentGroup, SegmentGroupUpdateStruct $updateStruct): SegmentGroup
    {
        return $this->innerService->updateSegmentGroup($segmentGroup, $updateStruct);
    }

    public function removeSegmentGroup(SegmentGroup $segmentGroup): void
    {
        $this->innerService->removeSegmentGroup($segmentGroup);
    }

    public function loadSegmentGroups(): array
    {
        return $this->innerService->loadSegmentGroups();
    }

    public function loadSegmentsAssignedToUser(User $user): array
    {
        return $this->innerService->loadSegmentsAssignedToUser($user);
    }

    public function loadSegmentsAssignedToCurrentUser(): array
    {
        return $this->innerService->loadSegmentsAssignedToCurrentUser();
    }

    public function isUserAssignedToSegment(User $user, Segment $segment): bool
    {
        return $this->innerService->isUserAssignedToSegment($user, $segment);
    }

    public function assignUserToSegment(User $user, Segment $segment): void
    {
        $this->innerService->assignUserToSegment($user, $segment);
    }

    public function unassignUserFromSegment(User $user, Segment $segment): void
    {
        $this->innerService->unassignUserFromSegment($user, $segment);
    }
}
