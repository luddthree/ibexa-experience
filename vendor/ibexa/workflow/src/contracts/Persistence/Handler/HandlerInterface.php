<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Persistence\Handler;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Workflow\Value\Persistence\MarkingMetadataSetStruct;
use Ibexa\Workflow\Value\Persistence\TransitionMetadata;
use Ibexa\Workflow\Value\Persistence\TransitionMetadataCreateStruct;
use Ibexa\Workflow\Value\Persistence\VersionLock;
use Ibexa\Workflow\Value\Persistence\WorkflowMetadata;
use Ibexa\Workflow\Value\Persistence\WorkflowMetadataCreateStruct;

interface HandlerInterface
{
    public function create(WorkflowMetadataCreateStruct $createStruct): WorkflowMetadata;

    public function createTransitionMetadata(
        TransitionMetadataCreateStruct $createStruct,
        int $workflowId
    ): TransitionMetadata;

    public function deleteWorkflowMetadata(int $workflowId): void;

    public function deleteTransitionMetadata(int $transitionId): void;

    public function deleteTransitionMetadataForWorkflow(int $workflowId): void;

    /**
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function load(int $contentId, int $versionNo, ?string $workflowName = null): WorkflowMetadata;

    /**
     * @return \Ibexa\Workflow\Value\Persistence\TransitionMetadata[]
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function loadTransitionMetadataByWorkflowId(int $workflowId): array;

    /**
     * @return \Ibexa\Workflow\Value\Persistence\WorkflowMetadata[]
     */
    public function loadAllWorkflowMetadataOriginatedByUser(int $userId, ?string $workflowName = null): array;

    /**
     * @return \Ibexa\Workflow\Value\Persistence\MarkingMetadata[]
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function loadMarkingMetadataByWorkflowId(int $workflowId): array;

    /**
     * @return \Ibexa\Workflow\Value\Persistence\MarkingMetadata[]
     */
    public function setMarkingMetadata(
        MarkingMetadataSetStruct $setStruct,
        int $workflowId
    ): array;

    /**
     * @return \Ibexa\Workflow\Value\Persistence\WorkflowMetadata[]
     */
    public function findWorkflowMetadata(Criterion $filter, int $limit = 10, int $offset = 0): array;

    /**
     * @return \Ibexa\Workflow\Value\Persistence\WorkflowMetadata[]
     */
    public function loadAllWorkflowMetadata(): array;

    /**
     * @return \Ibexa\Workflow\Value\Persistence\WorkflowMetadata[]
     */
    public function loadWorkflowMetadataByContent(int $contentId, ?int $versionNo = null): array;

    /**
     * @return \Ibexa\Workflow\Value\Persistence\WorkflowMetadata[]
     */
    public function loadWorkflowMetadataByStage(string $workflowName, string $stageName): array;

    public function createVersionLock(int $contentId, int $versionNo, int $userId): void;

    public function updateVersionLock(int $contentId, int $versionNo, bool $isLocked, ?int $userId = null): void;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If version lock is not found
     */
    public function getVersionLock(int $contentId, int $versionNo): VersionLock;

    public function deleteVersionLock(int $contentId, int $versionNo = null): void;

    public function deleteVersionLocksByUserId(int $userId): void;

    public function isVersionLocked(int $contentId, int $versionNo, ?int $userId = null): bool;

    public function cleanupWorkflowMetadataForContent(int $contentId): void;

    /**
     * @return int[]
     */
    public function loadContentIdsWithOrphanedWorkflowMetadata(): array;

    public function countContentWithOrphanedWorkflowMetadata(): int;

    public function countWorkflowMetadata(Criterion $filter): int;
}

class_alias(HandlerInterface::class, 'EzSystems\EzPlatformWorkflow\Persistence\Handler\HandlerInterface');
