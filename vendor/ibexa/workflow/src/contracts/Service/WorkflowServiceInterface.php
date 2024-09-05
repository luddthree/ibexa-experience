<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Service;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Ibexa\Workflow\Value\VersionLock;
use Ibexa\Workflow\Value\WorkflowMetadata;
use Ibexa\Workflow\Value\WorkflowMetadataList;
use Ibexa\Workflow\Value\WorkflowMetadataQuery;

interface WorkflowServiceInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param string|null $name
     *
     * @return \Ibexa\Workflow\Value\WorkflowMetadata
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function loadWorkflowMetadataForContent(Content $content, ?string $name = null): WorkflowMetadata;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param string|null $name
     *
     * @return \Ibexa\Workflow\Value\WorkflowMetadata
     */
    public function start(Content $content, ?string $name = null): WorkflowMetadata;

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata $workflow
     * @param string $transitionName
     *
     * @return bool
     */
    public function can(WorkflowMetadata $workflow, string $transitionName): bool;

    /**
     * Loads all workflow metadata started by $user.
     *
     * Loading all workflow metadata is highly discouraged, it leads to performance
     * issues. Please use only if you understand the consequences.
     *
     * @deprecated This method is deprecated since eZ Platform 3.0 and will be removed in 4.0.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\UserReference $user
     * @param string|null $name
     *
     * @return \Ibexa\Workflow\Value\WorkflowMetadata[]
     */
    public function loadWorkflowMetadataOriginatedByUser(UserReference $user, ?string $name = null): array;

    /**
     * Loads all workflow metadata stored in the database.
     *
     * Loading all workflow metadata is highly discouraged, it leads to performance
     * issues. Please use only if you understand the consequences.
     *
     * @deprecated This method is deprecated since eZ Platform 3.0 and will be removed in 4.0.
     *
     * @return \Ibexa\Workflow\Value\WorkflowMetadata[]
     */
    public function loadAllWorkflowMetadata(): array;

    public function loadWorkflowMetadataList(WorkflowMetadataQuery $query): WorkflowMetadataList;

    /**
     * @return \Ibexa\Workflow\Value\WorkflowMetadata[]
     */
    public function loadOngoingWorkflowMetadata(
        int $limit = 10,
        int $offset = 0
    ): array;

    /**
     * @return \Ibexa\Workflow\Value\WorkflowMetadata[]
     */
    public function loadOngoingWorkflowMetadataOriginatedByUser(
        UserReference $user,
        ?string $name = null,
        int $limit = 10,
        int $offset = 0
    ): array;

    /**
     * @param string $workflowName
     * @param string $stageName
     *
     * @return \Ibexa\Workflow\Value\WorkflowMetadata[]
     */
    public function loadWorkflowMetadataByStage(string $workflowName, string $stageName): array;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getVersionLock(VersionInfo $versionInfo): VersionLock;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getVersionLockByContentId(int $contentId, int $versionNo): VersionLock;

    public function isVersionLocked(VersionInfo $versionInfo, ?int $userId = null): bool;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    public function lockVersion(VersionInfo $versionInfo, int $userId = null): void;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function unlockVersion(VersionInfo $versionInfo): void;

    public function deleteLock(VersionLock $versionLock): void;

    public function deleteUserLocks(int $userId): void;

    public function deleteContentLocks(int $contentId): void;
}

class_alias(WorkflowServiceInterface::class, 'EzSystems\EzPlatformWorkflow\Service\WorkflowServiceInterface');
