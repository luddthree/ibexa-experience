<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Persistence\Gateway;

use Ibexa\Contracts\Core\FieldType\StorageGateway;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

/**
 * Gateway for workflow metadata.
 */
abstract class AbstractGateway extends StorageGateway
{
    /**
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    abstract public function getWorkflowById(int $workflowId): array;

    /**
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    abstract public function getWorkflowForContent(int $contentId, int $versionNo, string $workflowName): array;

    abstract public function getAllWorkflowMetadataOriginatedByUser(int $userId, ?string $workflowName = null): array;

    abstract public function insertWorkflow(
        int $contentId,
        int $versionNo,
        string $workflowName,
        int $initialUserId,
        int $startDate
    ): int;

    abstract public function deleteWorkflowMetadata(int $workflowId): void;

    abstract public function insertTransitionMetadata(
        int $workflowId,
        string $transitionName,
        int $userId,
        int $date,
        string $comment
    ): int;

    abstract public function deleteTransitionMetadata(int $transitionId): void;

    abstract public function deleteTransitionMetadataForWorkflow(int $workflowId): void;

    abstract public function loadAllTransitionMetadataByWorkflowId(int $workflowId): array;

    /**
     * @param string[] $places
     *
     * @return int[]
     */
    abstract public function setMarking(
        int $workflowId,
        array $places,
        string $message = '',
        ?int $reviewerId = null,
        array $result = []
    ): array;

    /**
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    abstract public function getMarkingByWorkflowId(int $workflowId): array;

    abstract public function deleteMarkingForWorkflow(int $workflowId): void;

    abstract public function getAllWorkflowMetadata(): array;

    abstract public function findWorkflowMetadata(Criterion $filter, int $limit = 10, int $offset = 0): array;

    abstract public function getWorkflowMetadataByContent(int $contentId, ?int $versionNo = null): array;

    abstract public function getWorkflowMetadataByStage(string $workflowName, string $stageName): array;

    abstract public function createVersionLock(
        int $contentId,
        int $versionNo,
        bool $isLocked,
        int $userId
    ): int;

    abstract public function updateVersionLock(
        int $contentId,
        int $versionNo,
        ?bool $isLocked = null,
        ?int $userId = null
    ): void;

    abstract public function getVersionLock(
        int $contentId,
        int $versionNo
    ): ?array;

    abstract public function isVersionLocked(
        int $contentId,
        int $versionNo,
        ?int $userId = null
    ): bool;

    abstract public function deleteVersionLock(
        int $contentId,
        int $versionNo = null
    ): void;

    abstract public function deleteVersionLockByUserId(int $userId): void;

    abstract public function getOrphanedWorkflowIdsByContentId(int $contentId): array;

    abstract public function findContentWithOrphanedWorkflowMetadata(): array;

    abstract public function countContentWithOrphanedWorkflowMetadata(): int;

    abstract public function countWorkflowMetadata(Criterion $filter): int;
}

class_alias(AbstractGateway::class, 'EzSystems\EzPlatformWorkflow\Persistence\Gateway\AbstractGateway');
