<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Persistence\Handler;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException as NotFound;
use Ibexa\Workflow\Persistence\Gateway\AbstractGateway;
use Ibexa\Workflow\Value\Persistence\MarkingMetadata;
use Ibexa\Workflow\Value\Persistence\MarkingMetadataSetStruct;
use Ibexa\Workflow\Value\Persistence\TransitionMetadata;
use Ibexa\Workflow\Value\Persistence\TransitionMetadataCreateStruct;
use Ibexa\Workflow\Value\Persistence\VersionLock;
use Ibexa\Workflow\Value\Persistence\WorkflowMetadata;
use Ibexa\Workflow\Value\Persistence\WorkflowMetadataCreateStruct;

class WorkflowHandler implements HandlerInterface
{
    /** @var \Ibexa\Workflow\Persistence\Gateway\AbstractGateway */
    private $gateway;

    public function __construct(AbstractGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * {@inheritdoc}
     */
    public function create(WorkflowMetadataCreateStruct $createStruct): WorkflowMetadata
    {
        $id = $this->gateway->insertWorkflow(
            $createStruct->contentId,
            $createStruct->versionNo,
            $createStruct->name,
            $createStruct->initialOwnerId,
            $createStruct->startDate ?? time()
        );

        $this->gateway->setMarking($id, $createStruct->stages);

        $workflow = new WorkflowMetadata();
        $workflow->id = $id;
        $workflow->name = $createStruct->name;
        $workflow->contentId = $createStruct->contentId;
        $workflow->versionNo = $createStruct->versionNo;
        $workflow->initialOwnerId = $createStruct->initialOwnerId;
        $workflow->startDate = $createStruct->startDate;

        return $workflow;
    }

    public function deleteWorkflowMetadata(int $workflowId): void
    {
        $this->gateway->deleteTransitionMetadataForWorkflow($workflowId);
        $this->gateway->deleteMarkingForWorkflow($workflowId);
        $this->gateway->deleteWorkflowMetadata($workflowId);
    }

    /**
     * {@inheritdoc}
     */
    public function createTransitionMetadata(
        TransitionMetadataCreateStruct $createStruct,
        int $workflowId
    ): TransitionMetadata {
        $id = $this->gateway->insertTransitionMetadata(
            $workflowId,
            $createStruct->name,
            (int) $createStruct->userId,
            $createStruct->date,
            $createStruct->message
        );

        $transitionMetadata = new TransitionMetadata();
        $transitionMetadata->id = $id;
        $transitionMetadata->name = $createStruct->name;
        $transitionMetadata->userId = $createStruct->userId;
        $transitionMetadata->workflowId = $workflowId;
        $transitionMetadata->message = $createStruct->message;
        $transitionMetadata->date = $createStruct->date;

        return $transitionMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTransitionMetadata(int $transitionId): void
    {
        $this->gateway->deleteTransitionMetadata($transitionId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTransitionMetadataForWorkflow(int $workflowId): void
    {
        $this->gateway->deleteTransitionMetadataForWorkflow($workflowId);
    }

    public function findWorkflowMetadata(Criterion $filter, int $limit = 10, int $offset = 0): array
    {
        $rawWorkflowMetadata = $this->gateway->findWorkflowMetadata($filter, $limit, $offset);

        return array_map([$this, 'mapToWorkflowMetadata'], $rawWorkflowMetadata);
    }

    /**
     * {@inheritdoc}
     */
    public function load(int $contentId, int $versionNo, ?string $workflowName = null): WorkflowMetadata
    {
        $rawWorkflowMetadata = $this->gateway->getWorkflowForContent($contentId, $versionNo, $workflowName);

        return $this->mapToWorkflowMetadata($rawWorkflowMetadata);
    }

    /**
     * {@inheritdoc}
     */
    public function loadTransitionMetadataByWorkflowId(int $workflowId): array
    {
        $data = $this->gateway->loadAllTransitionMetadataByWorkflowId($workflowId);

        $spiTransitionMetadataList = [];
        foreach ($data as $rawTransitionMetadata) {
            $spiTransitionMetadata = new TransitionMetadata();
            $spiTransitionMetadata->id = (int) $rawTransitionMetadata['id'];
            $spiTransitionMetadata->date = (int) $rawTransitionMetadata['timestamp'];
            $spiTransitionMetadata->message = $rawTransitionMetadata['comment'];
            $spiTransitionMetadata->userId = (int) $rawTransitionMetadata['user_id'];
            $spiTransitionMetadata->name = $rawTransitionMetadata['name'];

            $spiTransitionMetadataList[] = $spiTransitionMetadata;
        }

        return $spiTransitionMetadataList;
    }

    /**
     * {@inheritdoc}
     */
    public function loadAllWorkflowMetadataOriginatedByUser(int $userId, ?string $workflowName = null): array
    {
        $rawWorkflowMetadata = $this->gateway->getAllWorkflowMetadataOriginatedByUser($userId, $workflowName);

        return array_map([$this, 'mapToWorkflowMetadata'], $rawWorkflowMetadata);
    }

    /**
     * {@inheritdoc}
     */
    public function loadAllWorkflowMetadata(): array
    {
        $rawWorkflowMetadata = $this->gateway->getAllWorkflowMetadata();

        return array_map([$this, 'mapToWorkflowMetadata'], $rawWorkflowMetadata);
    }

    /**
     * {@inheritdoc}
     */
    public function loadWorkflowMetadataByContent(int $contentId, ?int $versionNo = null): array
    {
        $rawWorkflowMetadata = $this->gateway->getWorkflowMetadataByContent($contentId, $versionNo);

        return array_map([$this, 'mapToWorkflowMetadata'], $rawWorkflowMetadata);
    }

    /**
     * {@inheritdoc}
     */
    public function loadWorkflowMetadataByStage(string $workflowName, string $stageName): array
    {
        $rawWorkflowMetadata = $this->gateway->getWorkflowMetadataByStage($workflowName, $stageName);

        return array_map([$this, 'mapToWorkflowMetadata'], $rawWorkflowMetadata);
    }

    /**
     * {@inheritdoc}
     */
    public function loadMarkingMetadataByWorkflowId(int $workflowId): array
    {
        $rawMarkings = $this->gateway->getMarkingByWorkflowId($workflowId);

        $spiMarkingMetadataList = [];

        foreach ($rawMarkings as $rawMarking) {
            $spiMarkingMetadata = new MarkingMetadata();
            $spiMarkingMetadata->id = (int) $rawMarking['id'];
            $spiMarkingMetadata->workflowId = (int) $rawMarking['workflow_id'];
            $spiMarkingMetadata->name = $rawMarking['name'];
            $spiMarkingMetadata->reviewerId = isset($rawMarking['reviewer_id'])
                ? (int) $rawMarking['reviewer_id']
                : null;
            $spiMarkingMetadata->message = (string) $rawMarking['message'];
            $spiMarkingMetadata->result = isset($rawMarking['result'])
                ? json_decode($rawMarking['result'], true)
                : null;

            $spiMarkingMetadataList[] = $spiMarkingMetadata;
        }

        return $spiMarkingMetadataList;
    }

    /**
     * {@inheritdoc}
     */
    public function setMarkingMetadata(MarkingMetadataSetStruct $setStruct, int $workflowId): array
    {
        $this->gateway->deleteMarkingForWorkflow($workflowId);

        $ids = $this->gateway->setMarking(
            $workflowId,
            $setStruct->places,
            $setStruct->message,
            $setStruct->reviewerId,
            $setStruct->result->getResults()
        );

        $spiMarkingMetadataList = [];
        foreach ($ids as $key => $id) {
            $spiMarkingMetadata = new MarkingMetadata();
            $spiMarkingMetadata->id = (int) $id;
            $spiMarkingMetadata->name = $setStruct->places[$key];
            $spiMarkingMetadataList[] = $spiMarkingMetadata;
        }

        return $spiMarkingMetadataList;
    }

    public function cleanupWorkflowMetadataForContent(int $contentId): void
    {
        $orphanedWorkflowIds = $this->gateway->getOrphanedWorkflowIdsByContentId($contentId);

        foreach ($orphanedWorkflowIds as $orphanedWorkflowId) {
            $this->deleteWorkflowMetadata((int) $orphanedWorkflowId);
        }
    }

    public function countContentWithOrphanedWorkflowMetadata(): int
    {
        return $this->gateway->countContentWithOrphanedWorkflowMetadata();
    }

    public function loadContentIdsWithOrphanedWorkflowMetadata(): array
    {
        return array_map('intval', $this->gateway->findContentWithOrphanedWorkflowMetadata());
    }

    private function mapToWorkflowMetadata(array $rawWorkflowMetadata): WorkflowMetadata
    {
        $workflowMetadata = new WorkflowMetadata();
        $workflowMetadata->id = (int)$rawWorkflowMetadata['id'];
        $workflowMetadata->contentId = (int)$rawWorkflowMetadata['content_id'];
        $workflowMetadata->versionNo = (int)$rawWorkflowMetadata['version_no'];
        $workflowMetadata->name = $rawWorkflowMetadata['workflow_name'];
        $workflowMetadata->initialOwnerId = $rawWorkflowMetadata['initial_owner_id'];
        $workflowMetadata->startDate = $rawWorkflowMetadata['start_date'];

        return $workflowMetadata;
    }

    public function getVersionLock(
        int $contentId,
        int $versionNo
    ): VersionLock {
        $row = $this->gateway->getVersionLock($contentId, $versionNo);

        if (empty($row)) {
            throw new NotFound('version_lock', "contentId: $contentId, version: $versionNo");
        }

        $versionLock = new VersionLock();

        $versionLock->id = (int)$row['id'];
        $versionLock->contentId = (int)$row['content_id'];
        $versionLock->version = (int)$row['version'];
        $versionLock->userId = (int)$row['user_id'];
        $versionLock->created = (int)$row['created'];
        $versionLock->modified = (int)$row['modified'];
        $versionLock->isLocked = (bool)$row['is_locked'];

        return $versionLock;
    }

    public function deleteVersionLock(
        int $contentId,
        int $versionNo = null
    ): void {
        $this->gateway->deleteVersionLock($contentId, $versionNo);
    }

    public function deleteVersionLocksByUserId(int $userId): void
    {
        $this->gateway->deleteVersionLockByUserId($userId);
    }

    public function createVersionLock(
        int $contentId,
        int $versionNo,
        int $userId
    ): void {
        $this->gateway->createVersionLock(
            $contentId,
            $versionNo,
            true,
            $userId
        );
    }

    public function updateVersionLock(
        int $contentId,
        int $versionNo,
        bool $isLocked,
        ?int $userId = null
    ): void {
        $this->gateway->updateVersionLock(
            $contentId,
            $versionNo,
            $isLocked,
            $userId
        );
    }

    public function isVersionLocked(
        int $contentId,
        int $versionNo,
        ?int $userId = null
    ): bool {
        return $this->gateway->isVersionLocked($contentId, $versionNo, $userId);
    }

    public function countWorkflowMetadata(Criterion $filter): int
    {
        return $this->gateway->countWorkflowMetadata($filter);
    }
}

class_alias(WorkflowHandler::class, 'EzSystems\EzPlatformWorkflow\Persistence\Handler\WorkflowHandler');
