<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Service;

use DateTimeImmutable;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException as APIUnauthorizedException;
use Ibexa\Contracts\Core\Repository\PermissionCriterionResolver;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as ApiCriterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\Core\Base\Exceptions\NotFoundException as CoreNotFoundException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Workflow\Mapper\WorkflowMetadataValueMapper;
use Ibexa\Workflow\Search\Criterion;
use Ibexa\Workflow\Value\Persistence\WorkflowMetadataCreateStruct;
use Ibexa\Workflow\Value\VersionLock;
use Ibexa\Workflow\Value\WorkflowMetadata;
use Ibexa\Workflow\Value\WorkflowMetadataList;
use Ibexa\Workflow\Value\WorkflowMetadataQuery;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class WorkflowService implements WorkflowServiceInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const CONTENT_NOT_FOUND_ERROR_MESSAGE = 'The workflow metadata includes information about content that cannot be read.' .
    'The workflow is missing implementation that updates state after content is changed.';

    /** @var \Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface */
    private $workflowHandler;

    /** @var \Symfony\Component\Workflow\Registry */
    private $workflowRegistry;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionCriterionResolver */
    private $permissionCriterionResolver;

    /** @var \Ibexa\Workflow\Mapper\WorkflowMetadataValueMapper */
    private $workflowMetadataValueMapper;

    public function __construct(
        HandlerInterface $workflowHandler,
        WorkflowRegistryInterface $workflowRegistry,
        PermissionResolver $permissionResolver,
        PermissionCriterionResolver $permissionCriterionResolver,
        WorkflowMetadataValueMapper $workflowMetadataValueMapper
    ) {
        $this->workflowHandler = $workflowHandler;
        $this->workflowRegistry = $workflowRegistry;
        $this->permissionResolver = $permissionResolver;
        $this->permissionCriterionResolver = $permissionCriterionResolver;
        $this->workflowMetadataValueMapper = $workflowMetadataValueMapper;
        $this->logger = new NullLogger();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function loadWorkflowMetadataForContent(Content $content, ?string $name = null): WorkflowMetadata
    {
        if (!empty($name)) {
            $workflow = $this->workflowRegistry->getSupportedWorkflow($name, $content);
        } else {
            $workflows = $this->workflowRegistry->getSupportedWorkflows($content);
            $workflow = array_shift($workflows);
        }

        $spiWorkflow = $this->workflowHandler->load(
            (int)$content->id,
            (int)$content->getVersionInfo()->versionNo,
            $workflow->getName()
        );

        return $this->workflowMetadataValueMapper->fromPersistenceValue($spiWorkflow);
    }

    /**
     * {@inheritdoc}
     */
    public function start(Content $content, ?string $name = null): WorkflowMetadata
    {
        // @todo Add sanity check to prevent starting already existing workflows...

        $workflow = $this->workflowRegistry->getSupportedWorkflow($name, $content);

        $createStruct = new WorkflowMetadataCreateStruct();
        $createStruct->contentId = (int)$content->id;
        $createStruct->versionNo = (int)$content->getVersionInfo()->versionNo;
        $createStruct->name = $workflow->getName();
        $createStruct->initialOwnerId = $this->permissionResolver->getCurrentUserReference()->getUserId();
        $createStruct->startDate = time();
        $createStruct->stages = array_keys($workflow->getMarking($content)->getPlaces());

        $spiWorkflow = $this->workflowHandler->create($createStruct);

        return $this->workflowMetadataValueMapper->fromPersistenceValue($spiWorkflow);
    }

    /**
     * {@inheritdoc}
     */
    public function can(WorkflowMetadata $workflow, string $transitionName): bool
    {
        return $workflow->workflow->can($workflow->content, $transitionName);
    }

    /**
     * {@inheritdoc}
     */
    public function loadWorkflowMetadataOriginatedByUser(UserReference $user, ?string $name = null): array
    {
        $apiWorkflowMetadataList = [];
        $spiWorkflows = $this->workflowHandler->loadAllWorkflowMetadataOriginatedByUser((int)$user->getUserId(), $name);

        foreach ($spiWorkflows as $spiWorkflow) {
            try {
                $apiWorkflowMetadataList[] = $this->workflowMetadataValueMapper->fromPersistenceValue($spiWorkflow);
            } catch (NotFoundException $e) {
                @trigger_error(
                    self::CONTENT_NOT_FOUND_ERROR_MESSAGE,
                    E_USER_WARNING
                );
                continue;
            }
        }

        return $apiWorkflowMetadataList;
    }

    /**
     * {@inheritdoc}
     */
    public function loadAllWorkflowMetadata(): array
    {
        $apiWorkflowMetadataList = [];
        $spiWorkflows = $this->workflowHandler->loadAllWorkflowMetadata();

        foreach ($spiWorkflows as $spiWorkflow) {
            try {
                $apiWorkflowMetadataList[] = $this->workflowMetadataValueMapper->fromPersistenceValue($spiWorkflow);
            } catch (APIUnauthorizedException $e) {
                continue;
            } catch (NotFoundException $e) {
                @trigger_error(
                    self::CONTENT_NOT_FOUND_ERROR_MESSAGE,
                    E_USER_WARNING
                );
                continue;
            }
        }

        return $apiWorkflowMetadataList;
    }

    public function loadWorkflowMetadataList(WorkflowMetadataQuery $query): WorkflowMetadataList
    {
        $workflowMetadataList = new WorkflowMetadataList();

        $filter = null;
        if ($query->filter instanceof ApiCriterion) {
            $filter = $query->filter;
        }

        $permissionsCriterion = $this->permissionCriterionResolver->getPermissionsCriterion('content', 'edit');

        if ($permissionsCriterion === false) {
            return $workflowMetadataList;
        } elseif ($permissionsCriterion instanceof ApiCriterion) {
            if ($filter instanceof LogicalAnd) {
                $filter->criteria[] = new Criterion\Content($permissionsCriterion);
            } else {
                $filter = new LogicalAnd([$filter, new Criterion\Content($permissionsCriterion)]);
            }
        }

        $workflowMetadataList->totalCount = null;
        if ($query->performCount) {
            $workflowMetadataList->totalCount = $this->workflowHandler->countWorkflowMetadata($filter);
        }

        if ($workflowMetadataList->totalCount !== 0 && $query->limit > 0) {
            $persistenceWorkflows = $this->workflowHandler->findWorkflowMetadata($filter, $query->limit, $query->offset);

            foreach ($persistenceWorkflows as $persistenceWorkflow) {
                try {
                    $workflowMetadataList->items[] = $this->workflowMetadataValueMapper->fromPersistenceValue($persistenceWorkflow);
                } catch (UnauthorizedException $e) {
                    continue;
                } catch (NotFoundException $e) {
                    $this->logger->warning(self::CONTENT_NOT_FOUND_ERROR_MESSAGE, [
                        'exception' => $e,
                    ]);

                    continue;
                }
            }
        }

        return $workflowMetadataList;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException
     */
    public function loadOngoingWorkflowMetadata(
        int $limit = 10,
        int $offset = 0
    ): array {
        $criterion = new LogicalAnd([
            new Criterion\ContentIsOnLastStage(true),
            new Criterion\ContentIsOnInitialStage(true),
            new ApiCriterion\LogicalNot(new Criterion\ContentIsInTrash()),
        ]);

        return $this->buildOngoingWorkflowMetadata($limit, $offset, $criterion);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException
     */
    public function loadOngoingWorkflowMetadataOriginatedByUser(
        UserReference $user,
        ?string $name = null,
        int $limit = 10,
        int $offset = 0
    ): array {
        $criterion = new ApiCriterion\LogicalAnd([
            new Criterion\OriginatedByUserId($user->getUserId()),
            new Criterion\ContentIsOnLastStage(true),
            new Criterion\ContentIsOnInitialStage(true),
            new ApiCriterion\LogicalNot(new Criterion\ContentIsInTrash()),
        ]);

        return $this->buildOngoingWorkflowMetadata($limit, $offset, $criterion);
    }

    /**
     * @return array<\EzSystems\EzPlatformWorkflow\Value\WorkflowMetadata>
     */
    private function buildOngoingWorkflowMetadata(
        int $limit,
        int $offset,
        ApiCriterion\LogicalAnd $criterion
    ): array {
        $workflowMetadataList = [];
        $persistenceWorkflows = $this->workflowHandler->findWorkflowMetadata($criterion, $limit, $offset);

        foreach ($persistenceWorkflows as $persistenceWorkflow) {
            try {
                $workflowMetadataList[] = $this->workflowMetadataValueMapper->fromPersistenceValue(
                    $persistenceWorkflow
                );
            } catch (APIUnauthorizedException $e) {
                continue;
            } catch (NotFoundException $e) {
                @trigger_error(
                    self::CONTENT_NOT_FOUND_ERROR_MESSAGE,
                    E_USER_WARNING
                );
                continue;
            }
        }

        return $workflowMetadataList;
    }

    /**
     * {@inheritdoc}
     */
    public function loadWorkflowMetadataByStage(string $workflowName, string $stageName): array
    {
        $apiWorkflowMetadataList = [];
        $spiWorkflows = $this->workflowHandler->loadWorkflowMetadataByStage($workflowName, $stageName);

        foreach ($spiWorkflows as $spiWorkflow) {
            try {
                $apiWorkflowMetadataList[] = $this->workflowMetadataValueMapper->fromPersistenceValue($spiWorkflow);
            } catch (APIUnauthorizedException $e) {
                continue;
            } catch (NotFoundException $e) {
                @trigger_error(
                    self::CONTENT_NOT_FOUND_ERROR_MESSAGE,
                    E_USER_WARNING
                );
                continue;
            }
        }

        return $apiWorkflowMetadataList;
    }

    public function getVersionLock(VersionInfo $versionInfo): VersionLock
    {
        return $this->getVersionLockByContentId(
            $versionInfo->contentInfo->id,
            $versionInfo->versionNo
        );
    }

    public function getVersionLockByContentId(int $contentId, int $versionNo): VersionLock
    {
        try {
            $versionLock = $this->workflowHandler->getVersionLock(
                $contentId,
                $versionNo
            );

            return new VersionLock([
                'id' => $versionLock->id,
                'contentId' => $versionLock->contentId,
                'version' => $versionLock->version,
                'userId' => $versionLock->userId,
                'created' => (new DateTimeImmutable())->setTimestamp($versionLock->created),
                'modified' => (new DateTimeImmutable())->setTimestamp($versionLock->modified),
                'isLocked' => $versionLock->isLocked,
            ]);
        } catch (NotFoundException $exception) {
            throw new CoreNotFoundException(
                'VersionLock',
                [
                    'contentId' => $contentId,
                    'version' => $versionNo,
                ],
                $exception
            );
        }
    }

    public function isVersionLocked(VersionInfo $versionInfo, ?int $userId = null): bool
    {
        return $this->workflowHandler->isVersionLocked(
            $versionInfo->contentInfo->id,
            $versionInfo->versionNo,
            $userId
        );
    }

    public function lockVersion(
        VersionInfo $versionInfo,
        int $userId = null
    ): void {
        if (!$versionInfo->isDraft()) {
            throw new BadStateException('Version', 'The status is not draft');
        }

        $userId = $userId ?? $this->permissionResolver->getCurrentUserReference()->getUserId();

        try {
            $versionLock = $this->getVersionLock($versionInfo);

            $this->workflowHandler->updateVersionLock(
                $versionLock->contentId,
                $versionLock->version,
                true,
                $userId
            );
        } catch (NotFoundException $exception) {
            $this->workflowHandler->createVersionLock(
                $versionInfo->contentInfo->id,
                $versionInfo->versionNo,
                $userId
            );
        }
    }

    public function unlockVersion(VersionInfo $versionInfo): void
    {
        if (!$versionInfo->isDraft()) {
            throw new BadStateException('Version', 'The status is not draft');
        }

        if (!$this->permissionResolver->canUser('content', 'unlock', $versionInfo)) {
            throw new UnauthorizedException('content', 'unlock', [
                'contentId' => $versionInfo->contentInfo->id,
                'versionNo' => $versionInfo->versionNo,
            ]);
        }

        $this->workflowHandler->updateVersionLock(
            $versionInfo->contentInfo->id,
            $versionInfo->versionNo,
            false
        );
    }

    public function deleteLock(VersionLock $versionLock): void
    {
        $this->workflowHandler->deleteVersionLock(
            $versionLock->contentId,
            $versionLock->version
        );
    }

    public function deleteContentLocks(int $contentId): void
    {
        $this->workflowHandler->deleteVersionLock($contentId);
    }

    public function deleteUserLocks(int $userId): void
    {
        $this->workflowHandler->deleteVersionLocksByUserId($userId);
    }
}

class_alias(WorkflowService::class, 'EzSystems\EzPlatformWorkflow\Service\WorkflowService');
