<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Persistence\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Scheduler\Persistence\HandlerInterface;
use Ibexa\Scheduler\ValueObject\CreateScheduledEntryStruct;
use Ibexa\Scheduler\ValueObject\ScheduledEntry;
use Ibexa\Scheduler\ValueObject\UpdateScheduledEntry;

/**
 * DateBasedPublisher gateway implementation using the Doctrine database.
 */
class DoctrineDatabase implements HandlerInterface
{
    public const SCHEMA_TABLE_SCHEDULED_ENTRIES = 'ezdatebasedpublisher_scheduled_entries';

    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    /** @var \Doctrine\DBAL\Platforms\AbstractPlatform */
    private $dbPlatform;

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->dbPlatform = $connection->getDatabasePlatform();
    }

    public function insertVersionEntry(
        CreateScheduledEntryStruct $createScheduledEntryStruct
    ): bool {
        $query = $this->connection->createQueryBuilder();
        $query
            ->insert(self::SCHEMA_TABLE_SCHEDULED_ENTRIES)
            ->values(
                array_merge(
                    $this->getInsertEntryValues(
                        $createScheduledEntryStruct,
                        $query,
                    ),
                    [
                        'version_id' => $query->createPositionalParameter(
                            $createScheduledEntryStruct->versionId,
                            ParameterType::INTEGER
                        ),
                        'version_number' => $query->createPositionalParameter(
                            $createScheduledEntryStruct->versionNumber,
                            ParameterType::INTEGER
                        ),
                    ]
                )
            );

        return (bool)$query->execute();
    }

    public function insertContentEntry(
        CreateScheduledEntryStruct $createScheduledEntryStruct
    ): bool {
        $query = $this->connection->createQueryBuilder();
        $query
            ->insert(self::SCHEMA_TABLE_SCHEDULED_ENTRIES)
            ->values(
                $this->getInsertEntryValues($createScheduledEntryStruct, $query)
            );

        return (bool)$query->execute();
    }

    public function updateEntry(
        UpdateScheduledEntry $updateScheduledEntry
    ): bool {
        $query = $this->connection->createQueryBuilder();
        $query->update(self::SCHEMA_TABLE_SCHEDULED_ENTRIES);

        if (!empty($updateScheduledEntry->userId)) {
            $query->set(
                'user_id',
                $query->createPositionalParameter(
                    $updateScheduledEntry->userId,
                    ParameterType::INTEGER
                )
            );
        }

        if (!empty($updateScheduledEntry->actionTimestamp)) {
            $query->set(
                'action_timestamp',
                $query->createPositionalParameter(
                    $updateScheduledEntry->actionTimestamp,
                    ParameterType::INTEGER
                )
            );
        }

        $query->where(
            $query->expr()->eq(
                'id',
                $query->createPositionalParameter(
                    $updateScheduledEntry->id,
                    ParameterType::INTEGER
                )
            )
        );

        return (bool)$query->execute();
    }

    public function hasVersionEntry(int $versionId, ?string $action): bool
    {
        $query = $this->getCountQuery();

        $this->getVersionEntryCondition($versionId, $query);
        $this->addActionCondition($action, $query);

        $statement = $query->execute();
        $result = $statement->fetch(FetchMode::ASSOCIATIVE);

        return (bool)$result['count'];
    }

    public function hasContentEntry(int $contentId, ?string $action): bool
    {
        $query = $this->getCountQuery();

        $this->addContentEntryCondition($contentId, $query);
        $this->addActionCondition($action, $query);

        $result = $query->execute()->fetch(FetchMode::ASSOCIATIVE);

        return (bool)$result['count'];
    }

    public function getVersionEntry(int $versionId, ?string $action): ?ScheduledEntry
    {
        $query = $this->getSelectQuery();

        $this->getVersionEntryCondition($versionId, $query);
        $this->addActionCondition($action, $query);

        $statement = $query->execute();
        $result = $statement->fetch(FetchMode::ASSOCIATIVE);

        return $this->buildScheduledEntrySPIObject($result);
    }

    public function getContentEntry(int $contentId, ?string $action): ?ScheduledEntry
    {
        $query = $this->getSelectQuery();

        $this->addContentEntryCondition($contentId, $query);
        $this->addActionCondition($action, $query);

        $result = $query->execute()->fetch(FetchMode::ASSOCIATIVE);

        return $this->buildScheduledEntrySPIObject($result);
    }

    public function getEntriesByIds(array $scheduleVersionIds): iterable
    {
        $query = $this->getSelectQuery();
        $query->where(
            $query->expr()->in(
                'se.id',
                $query->createPositionalParameter($scheduleVersionIds, Connection::PARAM_INT_ARRAY)
            )
        );
        $query->orderBy('id', 'ASC');

        $statement = $query->execute();
        $rows = $statement->fetchAll(FetchMode::ASSOCIATIVE);

        return $this->buildScheduledEntrySPIObjectList($rows);
    }

    public function getVersionsEntries(?string $action, int $page = 0, int $limit = 25): iterable
    {
        $query = $this->getSelectQuery();

        $this->addVersionedCondition(true, $query);
        $this->addActionCondition($action, $query);

        return $this->getPaginatedResult($page, $limit, $query);
    }

    public function getUserVersionsEntries(
        int $userId,
        ?string $action,
        int $page = 0,
        int $limit = 25
    ): iterable {
        $query = $this->getSelectQuery();
        $expressionBuilder = $query->expr();

        $query->where(
            $expressionBuilder->eq(
                'se.user_id',
                $query->createPositionalParameter($userId, ParameterType::INTEGER)
            )
        );

        $this->addVersionedCondition(true, $query);
        $this->addActionCondition($action, $query);

        return $this->getPaginatedResult($page, $limit, $query);
    }

    public function getAllTypesEntries(
        int $contentId,
        ?string $action,
        int $page = 0,
        ?int $limit = 25
    ): iterable {
        $query = $this->getSelectQuery();
        $expressionBuilder = $query->expr();

        $query->where(
            $expressionBuilder->eq(
                'se.content_id',
                $query->createPositionalParameter($contentId, ParameterType::INTEGER)
            )
        );

        $this->addActionCondition($action, $query);

        return $this->getPaginatedResult($page, $limit, $query);
    }

    public function getVersionsEntriesOlderThan(
        int $timestamp,
        ?string $action,
        int $page = 0,
        int $limit = 25
    ): iterable {
        return $this->getEntriesOlderThan($timestamp, $action, true, $page, $limit);
    }

    public function getContentsEntriesOlderThan(
        int $timestamp,
        ?string $action,
        int $page = 0,
        int $limit = 25
    ): iterable {
        return $this->getEntriesOlderThan($timestamp, $action, false, $page, $limit);
    }

    public function getVersionsEntriesByDateRange(
        int $start,
        int $end,
        ?string $action,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable {
        $query = $this->getSelectQueryForEntriesInDateRangeCriteria(
            $this->getSelectQuery(),
            $start,
            $end,
            $action,
            $languages,
            $sinceId
        );

        $this->addVersionedCondition(true, $query);

        return $this->getPaginatedResult(0, $limit, $query);
    }

    public function getContentsEntriesByDateRange(
        int $start,
        int $end,
        ?string $action,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable {
        $query = $this->getSelectQueryForEntriesInDateRangeCriteria(
            $this->getSelectQuery(),
            $start,
            $end,
            $action,
            $languages,
            $sinceId
        );

        $this->addVersionedCondition(false, $query);

        return $this->getPaginatedResult(0, $limit, $query);
    }

    public function getVersionsEntriesForContent(
        int $contentId,
        ?string $action,
        int $page = 0,
        int $limit = 25
    ): iterable {
        $query = $this->getSelectQuery();
        $expressionBuilder = $query->expr();

        $query->where(
            $expressionBuilder->eq(
                'se.content_id',
                $query->createPositionalParameter($contentId, ParameterType::INTEGER)
            )
        );

        $this->addVersionedCondition(true, $query);
        $this->addActionCondition($action, $query);

        return $this->getPaginatedResult($page, $limit, $query);
    }

    public function countVersionsEntries(?string $action): int
    {
        $query = $this->getCountQuery();

        $this->addVersionedCondition(true, $query);
        $this->addActionCondition($action, $query);

        $statement = $query->execute();
        $versionCount = $statement->fetch(FetchMode::ASSOCIATIVE);

        return (int)$versionCount['count'];
    }

    public function countContentEntries(?string $action): int
    {
        $query = $this->getCountQuery();

        $this->addVersionedCondition(false, $query);
        $this->addActionCondition($action, $query);

        $statement = $query->execute();
        $versionCount = $statement->fetch(FetchMode::ASSOCIATIVE);

        return (int)$versionCount['count'];
    }

    public function countVersionsEntriesForContent(
        int $contentId,
        ?string $action
    ): int {
        $query = $this->getCountQuery();
        $expressionBuilder = $query->expr();

        $query->where(
            $expressionBuilder->eq(
                'se.content_id',
                $query->createPositionalParameter($contentId, ParameterType::INTEGER)
            )
        );

        $this->addVersionedCondition(true, $query);
        $this->addActionCondition($action, $query);

        $statement = $query->execute();
        $versionCount = $statement->fetch(FetchMode::ASSOCIATIVE);

        return (int)$versionCount['count'];
    }

    public function countUserVersionsEntries(int $userId, ?string $action): int
    {
        $query = $this->getCountQuery();

        $query->where(
            $query->expr()->eq(
                'se.user_id',
                $query->createPositionalParameter($userId, ParameterType::INTEGER)
            )
        );

        $this->addVersionedCondition(true, $query);
        $this->addActionCondition($action, $query);

        $statement = $query->execute();

        $versionCount = $statement->fetch(FetchMode::ASSOCIATIVE);

        return (int)$versionCount['count'];
    }

    public function countVersionsEntriesOlderThan(int $timestamp, ?string $action): int
    {
        return $this->countEntriesOlderThan($timestamp, true, $action);
    }

    public function countContentsEntriesOlderThan(int $timestamp, ?string $action): int
    {
        return $this->countEntriesOlderThan($timestamp, false, $action);
    }

    /**
     * @param int[] $languages
     */
    public function countVersionsEntriesInDateRange(
        int $start,
        int $end,
        ?string $action,
        array $languages = [],
        ?int $sinceId = null
    ): int {
        $query = $this->getSelectQueryForEntriesInDateRangeCriteria(
            $this->getCountQuery(),
            $start,
            $end,
            $action,
            $languages,
            $sinceId
        );

        $this->addVersionedCondition(true, $query);

        $statement = $query->execute();

        return (int)$statement->fetch(FetchMode::COLUMN);
    }

    /**
     * @param int[] $languages
     */
    public function countContentsEntriesInDateRange(
        int $start,
        int $end,
        ?string $action,
        array $languages = [],
        ?int $sinceId = null
    ): int {
        $query = $this->getSelectQueryForEntriesInDateRangeCriteria(
            $this->getCountQuery(),
            $start,
            $end,
            $action,
            $languages,
            $sinceId
        );

        $this->addVersionedCondition(false, $query);

        $statement = $query->execute();

        return (int)$statement->fetch(FetchMode::COLUMN);
    }

    public function deleteVersionEntry(int $versionId, ?string $action): bool
    {
        return $this->deleteEntriesByKey('version_id', $versionId, $action);
    }

    public function deleteContentEntry(int $contentId, ?string $action): bool
    {
        $query = $this->getDeleteQuery();

        $this->addContentEntryCondition($contentId, $query);
        $this->addActionCondition($action, $query);

        return (bool)$query->execute();
    }

    public function deleteAllTypesEntries(int $contentId, ?string $action): bool
    {
        return $this->deleteEntriesByKey('content_id', $contentId, $action);
    }

    private function getSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();

        $query->from(self::SCHEMA_TABLE_SCHEDULED_ENTRIES, 'se');
        $query->select(
            'se.id',
            'se.user_id',
            'se.content_id',
            'se.version_id',
            'se.version_number',
            'se.action_timestamp',
            'se.action',
            'se.url_root'
        );

        return $query;
    }

    private function getDeleteQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete(self::SCHEMA_TABLE_SCHEDULED_ENTRIES);

        return $query;
    }

    private function getCountQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select(
                sprintf('%s AS count', $this->dbPlatform->getCountExpression('se.id'))
            )
            ->from(self::SCHEMA_TABLE_SCHEDULED_ENTRIES, 'se');

        return $query;
    }

    /**
     * @param int[] $languages
     */
    private function getSelectQueryForEntriesInDateRangeCriteria(
        QueryBuilder $query,
        int $start,
        int $end,
        ?string $action,
        array $languages = [],
        ?int $sinceId = null
    ): QueryBuilder {
        $expr = $query->expr();

        $criteria = [];
        $criteria[] = $expr->gte(
            'se.action_timestamp',
            $query->createPositionalParameter($start, ParameterType::INTEGER),
        );

        $criteria[] = $expr->lt(
            'se.action_timestamp',
            $query->createPositionalParameter($end, ParameterType::INTEGER),
        );

        if (!empty($languages)) {
            $query->leftJoin(
                'se',
                'ezcontentobject_version',
                'ver',
                $expr->eq(
                    'ver.id',
                    'se.version_id',
                ),
            );

            $criteria[] = $expr->in(
                'ver.initial_language_id',
                $query->createPositionalParameter($languages, Connection::PARAM_INT_ARRAY)
            );
        }

        if ($sinceId !== null) {
            $criteria[] = $expr->gt(
                'se.id',
                $query->createPositionalParameter($sinceId, ParameterType::INTEGER)
            );
        }

        $query->where($expr->andX(...$criteria));

        $this->addActionCondition($action, $query);

        return $query;
    }

    private function buildScheduledEntrySPIObjectList(array $rows): iterable
    {
        $scheduledEntries = [];

        foreach ($rows as $row) {
            $scheduledEntries[] = $this->buildScheduledEntrySPIObject($row);
        }

        return $scheduledEntries;
    }

    /**
     * @param bool|array $row
     */
    private function buildScheduledEntrySPIObject($row): ?ScheduledEntry
    {
        if (empty($row) || false === is_array($row)) {
            return null;
        }

        return new ScheduledEntry(
            [
                'id' => (int)$row['id'],
                'userId' => (int)$row['user_id'],
                'contentId' => (int)$row['content_id'],
                'versionId' => $row['version_id'] ? (int)$row['version_id'] : null,
                'versionNumber' => $row['version_number'] ? (int)$row['version_number'] : null,
                'actionTimestamp' => (int)$row['action_timestamp'],
                'action' => $row['action'],
                'urlRoot' => $row['url_root'],
            ]
        );
    }

    private function getPaginatedResult(
        int $page,
        ?int $limit,
        QueryBuilder $query
    ): iterable {
        $query->addOrderBy('se.action_timestamp', 'ASC');
        $query->addOrderBy('se.id', 'ASC');

        if (null !== $limit) {
            $query->setMaxResults($limit);
            $query->setFirstResult($limit * $page);
        }

        $statement = $query->execute();
        $scheduledVersions = $statement->fetchAll(FetchMode::ASSOCIATIVE);

        return $this->buildScheduledEntrySPIObjectList($scheduledVersions);
    }

    private function deleteEntriesByKey(string $keyName, int $keyId, ?string $action): bool
    {
        $query = $this->getDeleteQuery();

        $query->where(
            $query->expr()->eq(
                $keyName,
                $query->createPositionalParameter($keyId, ParameterType::INTEGER)
            )
        );

        $this->addActionCondition($action, $query);

        return (bool)$query->execute();
    }

    private function addActionCondition(?string $action, QueryBuilder $queryBuilder): void
    {
        if (null !== $action) {
            $expr = $queryBuilder->expr();

            $queryBuilder->andWhere(
                $expr->eq(
                    'action',
                    $queryBuilder->createPositionalParameter(
                        $action,
                        ParameterType::STRING
                    )
                )
            );
        }
    }

    private function getVersionEntryCondition(int $versionId, QueryBuilder $query): void
    {
        $query->where(
            $query->expr()->eq(
                'version_id',
                $query->createPositionalParameter($versionId, ParameterType::INTEGER)
            )
        );

        $this->addVersionedCondition(true, $query);
    }

    private function addContentEntryCondition(int $contentId, QueryBuilder $query): void
    {
        $expressionBuilder = $query->expr();

        $query->where(
            $expressionBuilder->eq(
                'content_id',
                $query->createPositionalParameter($contentId, ParameterType::INTEGER)
            )
        );

        $this->addVersionedCondition(false, $query);
    }

    private function addVersionedCondition(bool $versioned, QueryBuilder $queryBuilder): void
    {
        $expressionBuilder = $queryBuilder->expr();

        if (true === $versioned) {
            $queryBuilder->andWhere(
                $expressionBuilder->isNotNull('version_id')
            );
        } else {
            $queryBuilder->andWhere(
                $expressionBuilder->isNull('version_id')
            );
        }
    }

    private function getInsertEntryValues(
        CreateScheduledEntryStruct $createScheduledEntryStruct,
        QueryBuilder $query,
        array $additionalEntryValues = []
    ): array {
        return [
            'content_id' => $query->createPositionalParameter(
                $createScheduledEntryStruct->contentId,
                ParameterType::INTEGER
            ),
            'user_id' => $query->createPositionalParameter(
                $createScheduledEntryStruct->userId,
                ParameterType::INTEGER
            ),
            'action_timestamp' => $query->createPositionalParameter(
                $createScheduledEntryStruct->actionTimestamp,
                ParameterType::INTEGER
            ),
            'action' => $query->createPositionalParameter(
                $createScheduledEntryStruct->action,
                ParameterType::STRING
            ),
            'url_root' => $query->createPositionalParameter(
                $createScheduledEntryStruct->urlRoot,
                ParameterType::STRING
            ),
        ];
    }

    private function countEntriesOlderThan(int $timestamp, bool $versioned, ?string $action): int
    {
        $query = $this->getCountQuery();
        $expressionBuilder = $query->expr();

        $query->where(
            $expressionBuilder->lt(
                'se.action_timestamp',
                $query->createPositionalParameter($timestamp, ParameterType::INTEGER)
            )
        );

        $this->addVersionedCondition($versioned, $query);
        $this->addActionCondition($action, $query);

        $statement = $query->execute();
        $versionCount = $statement->fetch(FetchMode::ASSOCIATIVE);

        return (int)$versionCount['count'];
    }

    private function getEntriesOlderThan(
        int $timestamp,
        ?string $action,
        bool $versioned,
        int $page = 0,
        int $limit = 25
    ): iterable {
        $query = $this->getSelectQuery();
        $query->where(
            $query->expr()->lt(
                'se.action_timestamp',
                $query->createPositionalParameter($timestamp, ParameterType::INTEGER)
            )
        );

        $this->addVersionedCondition($versioned, $query);
        $this->addActionCondition($action, $query);

        return $this->getPaginatedResult($page, $limit, $query);
    }
}

class_alias(DoctrineDatabase::class, 'EzSystems\DateBasedPublisher\Core\Persistence\Gateway\DoctrineDatabase');
