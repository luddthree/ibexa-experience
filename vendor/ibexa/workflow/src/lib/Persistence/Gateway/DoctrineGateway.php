<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Persistence\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Workflow\Exception\NotFoundException;

class DoctrineGateway extends AbstractGateway
{
    public const TABLE_WORKFLOWS = 'ezeditorialworkflow_workflows';
    public const TABLE_MARKINGS = 'ezeditorialworkflow_markings';
    public const TABLE_TRANSITIONS = 'ezeditorialworkflow_transitions';
    public const TABLE_VERSION_LOCK = 'ibexa_workflow_version_lock';

    /** @var \Doctrine\DBAL\Connection */
    protected $connection;

    /** @var \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter */
    private $criteriaConverter;

    public function __construct(Connection $connection, CriteriaConverter $criteriaConverter)
    {
        $this->connection = $connection;
        $this->criteriaConverter = $criteriaConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function insertWorkflow(
        int $contentId,
        int $versionNo,
        string $workflowName,
        int $initialOwnerId,
        int $startDate
    ): int {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert(self::TABLE_WORKFLOWS)
            ->values([
                'content_id' => ':content_id',
                'version_no' => ':version_no',
                'workflow_name' => ':workflow_name',
                'initial_owner_id' => ':initial_owner_id',
                'start_date' => ':start_date',
            ])
            ->setParameters([
                ':content_id' => $contentId,
                ':version_no' => $versionNo,
                ':workflow_name' => $workflowName,
                ':initial_owner_id' => $initialOwnerId,
                ':start_date' => $startDate,
            ]);

        $query->execute();

        return (int) $this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_WORKFLOWS, 'id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteWorkflowMetadata(int $workflowId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete(self::TABLE_WORKFLOWS)
            ->where($query->expr()->eq('id', ':id'))
            ->setParameter(':id', $workflowId, ParameterType::INTEGER)
        ;

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowById(int $workflowId): array
    {
        $selectQuery = $this->getSelectWorkflowMetadataQuery();

        $selectQuery
            ->where(
                $selectQuery->expr()->eq('w.id', ':id')
            )
            ->setParameter(':id', $workflowId, ParameterType::INTEGER)
        ;

        $statement = $selectQuery->execute();

        $data = $statement->fetch(FetchMode::ASSOCIATIVE);

        if (!$data) {
            throw new NotFoundException('Workflow data', sprintf('id: %d', $workflowId));
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowForContent(int $contentId, int $versionNo, string $workflowName): array
    {
        $selectQuery = $this->getSelectWorkflowMetadataQuery();

        $selectQuery
            ->where(
                $selectQuery->expr()->eq('w.content_id', ':content_id'),
                $selectQuery->expr()->eq('w.version_no', ':version_no'),
                $selectQuery->expr()->eq('w.workflow_name', ':workflow_name')
            )
            ->setParameter(':content_id', $contentId, ParameterType::INTEGER)
            ->setParameter(':version_no', $versionNo, ParameterType::INTEGER)
            ->setParameter(':workflow_name', $workflowName, ParameterType::STRING)
        ;

        $statement = $selectQuery->execute();

        $data = $statement->fetch(FetchMode::ASSOCIATIVE);

        if (!$data) {
            throw new NotFoundException(
                'Workflow data',
                sprintf('contentId: %d, versionNo: %d, workflowName: %s', $contentId, $versionNo, $workflowName)
            );
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllWorkflowMetadataOriginatedByUser(int $userId, ?string $workflowName = null): array
    {
        $selectQuery = $this->getSelectWorkflowMetadataQuery();

        $selectQuery
            ->where(
                $selectQuery->expr()->eq('w.initial_owner_id', ':initial_owner_id')
            )
            ->setParameter(':initial_owner_id', $userId, ParameterType::INTEGER)
        ;

        if (null !== $workflowName) {
            $selectQuery
                ->andWhere(
                    $selectQuery->expr()->eq('w.workflow_name', ':workflow_name')
                )
                ->setParameter(':workflow_name', $workflowName, ParameterType::STRING)
            ;
        }

        $statement = $selectQuery->execute();

        return $statement->fetchAll(FetchMode::ASSOCIATIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function findWorkflowMetadata(Criterion $filter, int $limit = 10, int $offset = 0): array
    {
        $selectQuery = $this->getSelectWorkflowMetadataQuery();

        $condition = $selectQuery->expr()->andX(
            $this->criteriaConverter->convertCriteria($selectQuery, $filter, [])
        );

        $selectQuery->where($condition);

        if ($limit > 0) {
            $selectQuery->setMaxResults($limit);
        }
        $selectQuery->setFirstResult($offset);

        $statement = $selectQuery->execute();

        return $statement->fetchAll(FetchMode::ASSOCIATIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllWorkflowMetadata(): array
    {
        $selectQuery = $this->getSelectWorkflowMetadataQuery();

        $statement = $selectQuery->execute();

        return $statement->fetchAll(FetchMode::ASSOCIATIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowMetadataByContent(int $contentId, ?int $versionNo = null): array
    {
        $selectQuery = $this->getSelectWorkflowMetadataQuery();
        $selectQuery
            ->andWhere(
                $selectQuery->expr()->eq('w.content_id', ':content_id')
            )
            ->setParameter(':content_id', $contentId, ParameterType::INTEGER)
        ;

        if (null !== $versionNo) {
            $selectQuery
                ->andWhere(
                    $selectQuery->expr()->eq('w.version_no', ':version_no')
                )
                ->setParameter(':version_no', $versionNo, ParameterType::INTEGER)
            ;
        }

        $statement = $selectQuery->execute();

        return $statement->fetchAll(FetchMode::ASSOCIATIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowMetadataByStage(string $workflowName, string $stageName): array
    {
        $selectQuery = $this->getSelectWorkflowMetadataQuery();
        $selectQuery
            ->leftJoin(
                'w',
                self::TABLE_MARKINGS,
                'm',
                'w.id = m.workflow_id'
            )
            ->leftJoin(
                'w',
                'ezcontentobject',
                'c',
                'w.content_id = c.id'
            )
            ->andWhere(
                $selectQuery->expr()->eq('w.workflow_name', ':workflow_name')
            )
            ->andWhere(
                $selectQuery->expr()->eq('m.name', ':stage_name')
            )
            ->andWhere(
                $selectQuery->expr()->neq('c.status', ':content_status')
            )
            ->setParameter(':workflow_name', $workflowName, ParameterType::STRING)
            ->setParameter(':stage_name', $stageName, ParameterType::STRING)
            ->setParameter(':content_status', ContentInfo::STATUS_TRASHED, ParameterType::INTEGER)
        ;

        $statement = $selectQuery->execute();

        return $statement->fetchAll(FetchMode::ASSOCIATIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function insertTransitionMetadata(
        int $workflowId,
        string $transitionName,
        int $userId,
        int $date,
        string $comment
    ): int {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert(self::TABLE_TRANSITIONS)
            ->values([
                'workflow_id' => ':workflow_id',
                'name' => ':name',
                'timestamp' => ':datetime',
                'user_id' => ':user_id',
                'comment' => ':comment',
            ])
            ->setParameters(
                [
                    ':workflow_id' => $workflowId,
                    ':name' => $transitionName,
                    ':datetime' => $date,
                    ':user_id' => $userId,
                    ':comment' => $comment,
                ],
                [
                    ParameterType::INTEGER,
                    ParameterType::STRING,
                    ParameterType::INTEGER,
                    ParameterType::INTEGER,
                    ParameterType::STRING,
                ]
            )
        ;

        $query->execute();

        return (int) $this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_TRANSITIONS, 'id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTransitionMetadata(int $transitionId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete(self::TABLE_TRANSITIONS)
            ->where([
                'id' => ':id',
            ])
            ->setParameters([
                ':id' => $transitionId,
            ])
        ;

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTransitionMetadataForWorkflow(int $workflowId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete(self::TABLE_TRANSITIONS)
            ->where($query->expr()->eq('workflow_id', ':workflow_id'))
            ->setParameter(':workflow_id', $workflowId, ParameterType::INTEGER)
        ;

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getTransitionsForWorkflow(int $workflowId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery
            ->select(
                'id',
                'name',
                'workflow_id',
                'user_id',
                'datetime',
                'comment'
            )
            ->from(self::TABLE_TRANSITIONS)
            ->where(
                $selectQuery->expr()->eq('workflow_id', ':workflow_id')
            )
            ->setParameter(':workflow_id', $workflowId, ParameterType::INTEGER)
        ;

        $statement = $selectQuery->execute();

        $data = $statement->fetchAll(FetchMode::ASSOCIATIVE);

        if (!$data) {
            throw new NotFoundException('Transition data', sprintf('workflowId: %d', $workflowId));
        }

        return $data;
    }

    public function setMarking(
        int $workflowId,
        array $places,
        string $message = '',
        ?int $reviewerId = null,
        array $result = []
    ): array {
        $ids = [];
        foreach ($places as $place) {
            $ids[] = $this->insertMarking($workflowId, $place, $message, $reviewerId, $result);
        }

        return $ids;
    }

    public function insertMarking(
        int $workflowId,
        string $place,
        string $message = '',
        ?int $reviewerId = null,
        array $result = []
    ): int {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert(self::TABLE_MARKINGS)
            ->values([
                'workflow_id' => ':workflow_id',
                'name' => ':name',
                'message' => ':message',
                'reviewer_id' => ':reviewer_id',
                'result' => ':result',
            ])
            ->setParameters(
                [
                    ':workflow_id' => $workflowId,
                    ':name' => $place,
                    ':message' => $message,
                    ':reviewer_id' => $reviewerId,
                    'result' => null === $result ? null : json_encode($result),
                ],
                [
                    ParameterType::INTEGER,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::INTEGER,
                    ParameterType::STRING,
                ]
            );

        $query->execute();

        return (int) $this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_MARKINGS, 'id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMarkingByWorkflowId(int $workflowId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery
            ->select(
                'id',
                'workflow_id',
                'name',
                'reviewer_id',
                'message',
                'result'
            )
            ->from(self::TABLE_MARKINGS)
            ->where(
                $selectQuery->expr()->eq('workflow_id', ':workflow_id')
            )
            ->setParameter(':workflow_id', $workflowId, ParameterType::INTEGER)
        ;

        $statement = $selectQuery->execute();

        $data = $statement->fetchAll(FetchMode::ASSOCIATIVE);

        if (!$data) {
            throw new NotFoundException('Marking data', sprintf('workflowId: %d', $workflowId));
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMarkingForWorkflow(int $workflowId): void
    {
        $deleteQuery = $this->connection->createQueryBuilder();

        $deleteQuery
            ->delete(self::TABLE_MARKINGS)
            ->where(
                $deleteQuery->expr()->eq('workflow_id', ':workflow_id')
            )
            ->setParameter(':workflow_id', $workflowId, ParameterType::INTEGER)
        ;

        $deleteQuery->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function loadAllTransitionMetadataByWorkflowId(int $workflowId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery
            ->select(
                'id',
                'workflow_id',
                'name',
                'timestamp',
                'user_id',
                'comment'
            )
            ->from(self::TABLE_TRANSITIONS)
            ->where(
                $selectQuery->expr()->eq('workflow_id', ':workflow_id')
            )
            ->setParameter(':workflow_id', $workflowId, ParameterType::INTEGER)
        ;

        $statement = $selectQuery->execute();

        return $statement->fetchAll(FetchMode::ASSOCIATIVE);
    }

    public function createVersionLock(
        int $contentId,
        int $versionNo,
        bool $isLocked,
        int $userId
    ): int {
        $query = $this->connection->createQueryBuilder();

        $query->insert(self::TABLE_VERSION_LOCK)
            ->values([
                'content_id' => $query->createPositionalParameter($contentId, ParameterType::INTEGER),
                'version' => $query->createPositionalParameter($versionNo, ParameterType::INTEGER),
                'user_id' => $query->createPositionalParameter($userId, ParameterType::INTEGER),
                'is_locked' => $query->createPositionalParameter($isLocked, ParameterType::BOOLEAN),
                'created' => $query->createPositionalParameter(time(), ParameterType::INTEGER),
                'modified' => $query->createPositionalParameter(time(), ParameterType::INTEGER),
            ]);

        $query->execute();

        return (int) $this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_VERSION_LOCK, 'id')
        );
    }

    public function updateVersionLock(
        int $contentId,
        int $versionNo,
        ?bool $isLocked = null,
        ?int $userId = null
    ): void {
        $query = $this->connection->createQueryBuilder();

        $query->update(self::TABLE_VERSION_LOCK)
            ->set('modified', ':now')
            ->where('content_id = :contentId')
            ->andWhere('version = :version');

        if (null !== $isLocked) {
            $query
                ->set('is_locked', ':isLocked')
                ->setParameter(':isLocked', $isLocked, ParameterType::BOOLEAN);
        }

        if (null !== $userId) {
            $query
                ->set('user_id', ':userId')
                ->setParameter(':userId', $userId, ParameterType::INTEGER);
        }

        $query
            ->setParameter(':now', time(), ParameterType::INTEGER)
            ->setParameter(':contentId', $contentId, ParameterType::INTEGER)
            ->setParameter(':version', $versionNo, ParameterType::INTEGER);

        $query->execute();
    }

    public function getVersionLock(
        int $contentId,
        int $versionNo
    ): ?array {
        $query = $this->connection->createQueryBuilder();

        $query->select(
            'id',
            'content_id',
            'version',
            'user_id',
            'modified',
            'created',
            'is_locked'
        )->from(self::TABLE_VERSION_LOCK);

        $query
            ->where('content_id = :contentId')
            ->andWhere('version = :version')
            ->setParameter(':contentId', $contentId, ParameterType::INTEGER)
            ->setParameter(':version', $versionNo, ParameterType::INTEGER);

        $statement = $query->execute();

        $result = $statement->fetchAssociative();

        if (empty($result)) {
            throw new NotFoundException('version_lock', "content_id: $contentId, version: $versionNo");
        }

        return $result;
    }

    public function isVersionLocked(
        int $contentId,
        int $versionNo,
        ?int $userId = null
    ): bool {
        $query = $this->connection->createQueryBuilder();

        $query->select('COUNT(id)')
            ->from(self::TABLE_VERSION_LOCK);

        $query
            ->where('content_id = :contentId')
            ->andWhere('version = :version')
            ->andWhere('is_locked = :isLocked')
            ->setParameter(':contentId', $contentId, ParameterType::INTEGER)
            ->setParameter(':version', $versionNo, ParameterType::INTEGER)
            ->setParameter(':isLocked', true, ParameterType::BOOLEAN);

        if (null !== $userId) {
            $query->andWhere('user_id = :userId');
            $query->setParameter(':userId', $userId, ParameterType::INTEGER);
        }

        $statement = $query->execute();

        return $statement->fetchOne() > 0;
    }

    public function deleteVersionLock(
        int $contentId,
        int $versionNo = null
    ): void {
        $query = $this->connection->createQueryBuilder();

        $query->delete(self::TABLE_VERSION_LOCK)
            ->where('content_id = :contentId')
            ->setParameter(':contentId', $contentId, ParameterType::INTEGER);

        if (null !== $versionNo) {
            $query->andWhere('version = :version')
                ->setParameter(':version', $versionNo, ParameterType::INTEGER);
        }

        $query->execute();
    }

    public function deleteVersionLockByUserId(int $userId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete(self::TABLE_VERSION_LOCK)
            ->where('user_id = :userId')
            ->setParameter(':userId', $userId, ParameterType::INTEGER);

        $query->execute();
    }

    public function getOrphanedWorkflowIdsByContentId(int $contentId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery
            ->select(
                'w.id'
            )
            ->from(self::TABLE_WORKFLOWS, 'w')
            ->leftJoin(
                'w',
                'ezcontentobject_version',
                'v',
                $selectQuery->expr()->and(
                    $selectQuery->expr()->eq('w.content_id', 'v.contentobject_id'),
                    $selectQuery->expr()->eq('w.version_no', 'v.version')
                )
            )
            ->where(
                $selectQuery->expr()->eq('w.content_id', ':content_id')
            )
            ->andWhere(
                $selectQuery->expr()->isNull('v.version')
            )
            ->setParameter(':content_id', $contentId, ParameterType::INTEGER)
        ;

        $statement = $selectQuery->execute();

        return $statement->fetchFirstColumn();
    }

    public function findContentWithOrphanedWorkflowMetadata(): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery
            ->select(
                'w.content_id'
            )
            ->from(self::TABLE_WORKFLOWS, 'w')
            ->leftJoin(
                'w',
                'ezcontentobject_version',
                'v',
                $selectQuery->expr()->and(
                    $selectQuery->expr()->eq('w.content_id', 'v.contentobject_id'),
                    $selectQuery->expr()->eq('w.version_no', 'v.version')
                )
            )
            ->where(
                $selectQuery->expr()->isNull('v.version')
            )
        ;

        $statement = $selectQuery->execute();

        return $statement->fetchFirstColumn();
    }

    public function countContentWithOrphanedWorkflowMetadata(): int
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery
            ->select(
                $this->connection->getDatabasePlatform()->getCountExpression('w.content_id')
            )
            ->from(self::TABLE_WORKFLOWS, 'w')
            ->leftJoin('w', 'ezcontentobject_version', 'v', $selectQuery->expr()->and(
                $selectQuery->expr()->eq('w.content_id', 'v.contentobject_id'),
                $selectQuery->expr()->eq('w.version_no', 'v.version')
            ))
            ->where(
                $selectQuery->expr()->isNull('v.version')
            )
        ;

        $statement = $selectQuery->execute();

        return (int) $statement->fetchOne();
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getSelectWorkflowMetadataQuery(): QueryBuilder
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery
            ->select(
                'w.id',
                'w.content_id',
                'w.version_no',
                'w.workflow_name',
                'w.initial_owner_id',
                'w.start_date'
            )
            ->from(self::TABLE_WORKFLOWS, 'w')
        ;

        return $selectQuery;
    }

    public function countWorkflowMetadata(Criterion $filter): int
    {
        $countQuery = new FilteringQueryBuilder($this->connection);
        $countQuery->select('COUNT(w.id)')->from(self::TABLE_WORKFLOWS, 'w');
        $countQuery->andWhere($this->criteriaConverter->convertCriteria($countQuery, $filter, []));

        return (int) $countQuery->execute()->fetchOne();
    }
}

class_alias(DoctrineGateway::class, 'EzSystems\EzPlatformWorkflow\Persistence\Gateway\DoctrineGateway');
