<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Persistence\Gateway;

/**
 * @internal
 */
interface GatewayInterface
{
    /**
     * @return array<string, string>
     */
    public function loadSegmentById(int $id): array;

    /**
     * @return array<string, string>
     */
    public function loadSegmentByIdentifier(string $identifier): array;

    public function insertSegment(string $identifier, string $name): int;

    public function updateSegment(int $segmentId, string $identifier, string $name): void;

    public function removeSegment(int $segmentId): void;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function loadSegmentsAssignedToGroup(int $segmentGroupId): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function loadSegmentsAssignedToUser(int $userId): array;

    /**
     * @return array<string, mixed>
     */
    public function loadSegmentGroupById(int $id): array;

    /**
     * @return array<string, mixed>
     */
    public function loadSegmentGroupByIdentifier(string $identifier): array;

    public function insertSegmentGroup(string $identifier, string $name): int;

    public function updateSegmentGroup(int $segmentGroupId, string $identifier, string $name): void;

    public function removeSegmentGroup(int $segmentGroupId): void;

    /**
     * @return array<array<string, mixed>>
     */
    public function loadSegmentGroups(): array;

    public function assignSegmentToGroup(int $segmentId, int $segmentGroupId): void;

    public function unassignSegmentFromGroup(int $segmentId, int $segmentGroupId): void;

    public function isUserAssignedToSegment(int $userId, int $segmentId): bool;

    public function assignUserToSegment(int $userId, int $segmentId): void;

    public function unassignUserFromSegment(int $userId, int $segmentId): void;

    public function unassignUsersFromSegment(int $segmentId): void;
}

class_alias(GatewayInterface::class, 'Ibexa\Platform\Segmentation\Persistence\Gateway\GatewayInterface');
