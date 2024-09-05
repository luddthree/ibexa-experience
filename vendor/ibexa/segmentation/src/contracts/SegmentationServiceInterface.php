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

interface SegmentationServiceInterface
{
    /**
     * @deprecated Deprecated since Ibexa DXP 3.3 and will be dropped in 5.0. Use loadSegmentByIdentifier method instead.
     */
    public function loadSegment(int $segmentId): Segment;

    public function loadSegmentByIdentifier(string $identifier): Segment;

    public function createSegment(SegmentCreateStruct $createStruct): Segment;

    /**
     * @param \Ibexa\Segmentation\Value\Segment $segment
     */
    public function updateSegment($segment, SegmentUpdateStruct $updateStruct): Segment;

    public function removeSegment(Segment $segment): void;

    /**
     * @return \Ibexa\Segmentation\Value\Segment[]
     */
    public function loadSegmentsAssignedToGroup(SegmentGroup $segmentGroup): array;

    /**
     * @deprecated Deprecated since Ibexa DXP 3.3 and will be dropped in 5.0. Use loadSegmentGroupByIdentifier method instead.
     */
    public function loadSegmentGroup(int $segmentGroupId): SegmentGroup;

    public function loadSegmentGroupByIdentifier(string $identifier): SegmentGroup;

    public function createSegmentGroup(SegmentGroupCreateStruct $createStruct): SegmentGroup;

    /**
     * @param \Ibexa\Segmentation\Value\SegmentGroup $segmentGroup
     */
    public function updateSegmentGroup($segmentGroup, SegmentGroupUpdateStruct $updateStruct): SegmentGroup;

    public function removeSegmentGroup(SegmentGroup $segmentGroup): void;

    /**
     * @return \Ibexa\Segmentation\Value\SegmentGroup[]
     */
    public function loadSegmentGroups(): array;

    /**
     * @return \Ibexa\Segmentation\Value\Segment[]
     */
    public function loadSegmentsAssignedToUser(User $user): array;

    /**
     * @return \Ibexa\Segmentation\Value\Segment[]
     */
    public function loadSegmentsAssignedToCurrentUser(): array;

    public function isUserAssignedToSegment(User $user, Segment $segment): bool;

    public function assignUserToSegment(User $user, Segment $segment): void;

    public function unassignUserFromSegment(User $user, Segment $segment): void;
}

class_alias(SegmentationServiceInterface::class, 'Ibexa\Platform\Contracts\Segmentation\SegmentationServiceInterface');
