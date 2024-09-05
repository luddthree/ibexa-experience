<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Persistence\Handler;

use Ibexa\Segmentation\Value\Persistence\Segment;
use Ibexa\Segmentation\Value\Persistence\SegmentCreateStruct;
use Ibexa\Segmentation\Value\Persistence\SegmentGroup;
use Ibexa\Segmentation\Value\Persistence\SegmentGroupCreateStruct;
use Ibexa\Segmentation\Value\Persistence\SegmentGroupUpdateStruct;
use Ibexa\Segmentation\Value\Persistence\SegmentUpdateStruct;

interface HandlerInterface
{
    public function loadSegmentById(int $id): Segment;

    public function loadSegmentByIdentifier(string $identifier): Segment;

    public function createSegment(SegmentCreateStruct $createStruct): Segment;

    public function updateSegment(Segment $segment, SegmentUpdateStruct $updateStruct): Segment;

    public function removeSegment(Segment $segment): void;

    /**
     * @return array<\Ibexa\Segmentation\Value\Persistence\Segment>
     */
    public function loadSegmentsAssignedToGroup(SegmentGroup $segmentGroup): array;

    /**
     * @return array<\Ibexa\Segmentation\Value\Persistence\Segment>
     */
    public function loadSegmentsAssignedToUser(int $userId): array;

    public function loadSegmentGroupById(int $id): SegmentGroup;

    public function loadSegmentGroupByIdentifier(string $identifier): SegmentGroup;

    public function createSegmentGroup(SegmentGroupCreateStruct $createStruct): SegmentGroup;

    public function updateSegmentGroup(SegmentGroup $segmentGroup, SegmentGroupUpdateStruct $updateStruct): SegmentGroup;

    public function removeSegmentGroup(SegmentGroup $segmentGroup): void;

    /**
     * @return \Ibexa\Segmentation\Value\Persistence\SegmentGroup[]
     */
    public function loadSegmentGroups(): array;

    public function assignSegmentToGroup(Segment $segment, SegmentGroup $segmentGroup): void;

    public function unassignSegmentFromGroup(Segment $segment, SegmentGroup $segmentGroup): void;

    public function isUserAssignedToSegment(int $userId, Segment $segment): bool;

    public function assignUserToSegment(int $userId, Segment $segment): void;

    public function unassignUserFromSegment(int $userId, Segment $segment): void;
}

class_alias(HandlerInterface::class, 'Ibexa\Platform\Segmentation\Persistence\Handler\HandlerInterface');
