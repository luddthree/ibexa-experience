<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Persistence\Handler;

use Ibexa\Segmentation\Persistence\Gateway\GatewayInterface;
use Ibexa\Segmentation\Value\Persistence\Segment;
use Ibexa\Segmentation\Value\Persistence\SegmentCreateStruct;
use Ibexa\Segmentation\Value\Persistence\SegmentGroup;
use Ibexa\Segmentation\Value\Persistence\SegmentGroupCreateStruct;
use Ibexa\Segmentation\Value\Persistence\SegmentGroupUpdateStruct;
use Ibexa\Segmentation\Value\Persistence\SegmentUpdateStruct;

class DatabaseHandler implements HandlerInterface
{
    /** @var \Ibexa\Segmentation\Persistence\Gateway\GatewayInterface */
    private $gateway;

    public function __construct(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    public function loadSegmentById(int $id): Segment
    {
        $data = $this->gateway->loadSegmentById($id);

        return $this->createSegmentFromData($data);
    }

    public function loadSegmentByIdentifier(string $identifier): Segment
    {
        $data = $this->gateway->loadSegmentByIdentifier($identifier);

        return $this->createSegmentFromData($data);
    }

    public function createSegment(SegmentCreateStruct $createStruct): Segment
    {
        $segmentId = $this->gateway->insertSegment($createStruct->identifier, $createStruct->name);
        $this->gateway->assignSegmentToGroup($segmentId, $createStruct->groupId);

        return $this->createSegmentFromData(
            $this->gateway->loadSegmentById($segmentId)
        );
    }

    public function updateSegment(Segment $segment, SegmentUpdateStruct $updateStruct): Segment
    {
        $this->gateway->updateSegment($segment->id, $updateStruct->identifier, $updateStruct->name);
        $this->gateway->unassignSegmentFromGroup($segment->id, $segment->groupId);
        $this->gateway->assignSegmentToGroup($segment->id, $updateStruct->groupId);

        return $this->createSegmentFromData(
            $this->gateway->loadSegmentById($segment->id)
        );
    }

    public function removeSegment(Segment $segment): void
    {
        $this->gateway->unassignUsersFromSegment($segment->id);
        $this->gateway->removeSegment($segment->id);
    }

    public function loadSegmentsAssignedToGroup(SegmentGroup $segmentGroup): array
    {
        $data = $this->gateway->loadSegmentsAssignedToGroup($segmentGroup->id);
        $segments = [];

        /** @var array<string, string> $segmentData */
        foreach ($data as $segmentData) {
            $segments[] = new Segment([
                'id' => (int) $segmentData['id'],
                'identifier' => $segmentData['identifier'],
                'name' => $segmentData['name'],
                'groupId' => (int) $segmentData['group_id'],
            ]);
        }

        return $segments;
    }

    public function loadSegmentsAssignedToUser(int $userId): array
    {
        $data = $this->gateway->loadSegmentsAssignedToUser($userId);
        $segments = [];

        /** @var array<string, string> $segmentData */
        foreach ($data as $segmentData) {
            $segments[] = new Segment([
                'id' => (int) $segmentData['id'],
                'identifier' => $segmentData['identifier'],
                'name' => $segmentData['name'],
                'groupId' => (int) $segmentData['group_id'],
            ]);
        }

        return $segments;
    }

    public function loadSegmentGroupById(int $id): SegmentGroup
    {
        /** @var array<string, string> $data */
        $data = $this->gateway->loadSegmentGroupById($id);

        return $this->createSegmentGroupFromData($data);
    }

    public function loadSegmentGroupByIdentifier(string $identifier): SegmentGroup
    {
        /** @var array<string, string> $data */
        $data = $this->gateway->loadSegmentGroupByIdentifier($identifier);

        return $this->createSegmentGroupFromData($data);
    }

    public function createSegmentGroup(SegmentGroupCreateStruct $createStruct): SegmentGroup
    {
        $id = $this->gateway->insertSegmentGroup($createStruct->identifier, $createStruct->name);

        /** @var array<string, string> $data */
        $data = $this->gateway->loadSegmentGroupById($id);

        return $this->createSegmentGroupFromData($data);
    }

    public function updateSegmentGroup(SegmentGroup $segmentGroup, SegmentGroupUpdateStruct $updateStruct): SegmentGroup
    {
        $this->gateway->updateSegmentGroup($segmentGroup->id, $updateStruct->identifier, $updateStruct->name);

        /** @var array<string, string> $data */
        $data = $this->gateway->loadSegmentGroupById($segmentGroup->id);

        return $this->createSegmentGroupFromData($data);
    }

    public function removeSegmentGroup(SegmentGroup $segmentGroup): void
    {
        $segments = $this->gateway->loadSegmentsAssignedToGroup($segmentGroup->id);
        /** @var array<string> $segmentIds */
        $segmentIds = array_column($segments, 'id');

        foreach ($segmentIds as $segmentId) {
            $segmentId = (int) $segmentId;
            $this->gateway->unassignUsersFromSegment($segmentId);
            $this->gateway->unassignSegmentFromGroup($segmentId, $segmentGroup->id);
            $this->gateway->removeSegment($segmentId);
        }

        $this->gateway->removeSegmentGroup($segmentGroup->id);
    }

    public function loadSegmentGroups(): array
    {
        $data = $this->gateway->loadSegmentGroups();

        $groups = [];

        /** @var array<string, string> $groupData */
        foreach ($data as $groupData) {
            $groups[] = $this->createSegmentGroupFromData($groupData);
        }

        return $groups;
    }

    public function assignSegmentToGroup(Segment $segment, SegmentGroup $segmentGroup): void
    {
        $this->gateway->assignSegmentToGroup($segment->id, $segmentGroup->id);
    }

    public function unassignSegmentFromGroup(Segment $segment, SegmentGroup $segmentGroup): void
    {
        $this->gateway->unassignSegmentFromGroup($segment->id, $segmentGroup->id);
    }

    public function isUserAssignedToSegment(int $userId, Segment $segment): bool
    {
        return $this->gateway->isUserAssignedToSegment($userId, $segment->id);
    }

    public function assignUserToSegment(int $userId, Segment $segment): void
    {
        $this->gateway->assignUserToSegment($userId, $segment->id);
    }

    public function unassignUserFromSegment(int $userId, Segment $segment): void
    {
        $this->gateway->unassignUserFromSegment($userId, $segment->id);
    }

    /**
     * @param array<string, string> $data
     */
    private function createSegmentFromData(array $data): Segment
    {
        return new Segment([
            'id' => (int) $data['id'],
            'identifier' => $data['identifier'],
            'name' => $data['name'],
            'groupId' => (int) $data['group_id'],
        ]);
    }

    /**
     * @param array<string, string> $data
     */
    private function createSegmentGroupFromData(array $data): SegmentGroup
    {
        return new SegmentGroup([
            'id' => (int) $data['id'],
            'identifier' => $data['identifier'],
            'name' => $data['name'],
        ]);
    }
}

class_alias(DatabaseHandler::class, 'Ibexa\Platform\Segmentation\Persistence\Handler\DatabaseHandler');
