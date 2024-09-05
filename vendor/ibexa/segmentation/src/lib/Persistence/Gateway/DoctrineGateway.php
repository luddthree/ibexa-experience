<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Persistence\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Segmentation\Exception\Persistence\SegmentGroupNotFoundException;
use Ibexa\Segmentation\Exception\Persistence\SegmentNotFoundException;

class DoctrineGateway implements GatewayInterface
{
    public const TABLE_SEGMENTS = 'ibexa_segments';
    public const TABLE_SEGMENT_GROUPS = 'ibexa_segment_groups';
    public const TABLE_SEGMENT_GROUP_MAP = 'ibexa_segment_group_map';
    public const TABLE_SEGMENT_USER_MAP = 'ibexa_segment_user_map';

    /** @var \Doctrine\DBAL\Connection */
    protected $connection;

    /**
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function loadSegmentById(int $id): array
    {
        $selectQuery = $this->buildFindSingleSegmentQuery();

        $selectQuery
            ->where(
                $selectQuery->expr()->eq(
                    $this->connection->quoteIdentifier('id'),
                    ':segment_id'
                )
            )
            ->setParameter(':segment_id', $id, ParameterType::INTEGER);

        /** @var array<string, string>|false $data */
        $data = $selectQuery->execute()->fetchAssociative();

        if (false === $data) {
            throw new SegmentNotFoundException("segmentId: $id");
        }

        return $data;
    }

    public function loadSegmentByIdentifier(string $identifier): array
    {
        $selectQuery = $this->buildFindSingleSegmentQuery();

        $selectQuery
            ->where(
                $selectQuery->expr()->eq(
                    $this->connection->quoteIdentifier('identifier'),
                    ':identifier'
                )
            )
            ->setParameter(':identifier', $identifier);

        /** @var array<string, string>|false $data */
        $data = $selectQuery->execute()->fetchAssociative();

        if (false === $data) {
            throw new SegmentNotFoundException("segmentIdentifier: $identifier");
        }

        return $data;
    }

    private function buildFindSingleSegmentQuery(): QueryBuilder
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select(
            $this->connection->quoteIdentifier('s.id'),
            $this->connection->quoteIdentifier('s.identifier'),
            $this->connection->quoteIdentifier('s.name'),
            $this->connection->quoteIdentifier('sgm.group_id'),
        );

        $selectQuery
            ->from($this->connection->quoteIdentifier(self::TABLE_SEGMENTS), 's')
            ->leftJoin(
                's',
                self::TABLE_SEGMENT_GROUP_MAP,
                'sgm',
                'sgm.segment_id = s.id'
            );

        return $selectQuery;
    }

    public function insertSegment(string $identifier, string $name): int
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_SEGMENTS))
            ->values([
                'identifier' => ':identifier',
                'name' => ':name',
            ])
            ->setParameters([
                ':identifier' => $identifier,
                ':name' => $name,
            ]);

        $query->execute();

        return (int)$this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_SEGMENTS, 'id')
        );
    }

    public function updateSegment(int $segmentId, string $identifier, string $name): void
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->update(self::TABLE_SEGMENTS)
            ->set(
                $this->connection->quoteIdentifier('identifier'),
                ':identifier'
            )
            ->set(
                $this->connection->quoteIdentifier('name'),
                ':name'
            )
            ->andWhere(
                $selectQuery->expr()->eq(
                    $this->connection->quoteIdentifier('id'),
                    ':segment_id'
                )
            )
            ->setParameter(':segment_id', $segmentId, ParameterType::INTEGER)
            ->setParameter(':identifier', $identifier, ParameterType::STRING)
            ->setParameter(':name', $name, ParameterType::STRING);

        $selectQuery->execute();
    }

    public function removeSegment(int $segmentId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_SEGMENTS))
            ->andWhere('id = :segment_id')
            ->setParameter(':segment_id', $segmentId, ParameterType::INTEGER);

        $query->execute();
    }

    public function loadSegmentsAssignedToGroup(int $segmentGroupId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select(
            $this->connection->quoteIdentifier('s.id'),
            $this->connection->quoteIdentifier('s.identifier'),
            $this->connection->quoteIdentifier('s.name'),
            $this->connection->quoteIdentifier('sgm.group_id')
        )
            ->from($this->connection->quoteIdentifier(self::TABLE_SEGMENTS), 's')
            ->leftJoin(
                's',
                self::TABLE_SEGMENT_GROUP_MAP,
                'sgm',
                'sgm.segment_id = s.id'
            )
            ->andWhere(
                $selectQuery->expr()->eq(
                    $this->connection->quoteIdentifier('sgm.group_id'),
                    ':group_id'
                )
            )
            ->setParameter(':group_id', $segmentGroupId, ParameterType::INTEGER);

        return $selectQuery->execute()->fetchAllAssociative();
    }

    public function loadSegmentsAssignedToUser(int $userId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select(
            $this->connection->quoteIdentifier('s.id'),
            $this->connection->quoteIdentifier('s.identifier'),
            $this->connection->quoteIdentifier('s.name'),
            $this->connection->quoteIdentifier('sgm.group_id')
        )
            ->from($this->connection->quoteIdentifier(self::TABLE_SEGMENTS), 's')
            ->leftJoin(
                's',
                self::TABLE_SEGMENT_GROUP_MAP,
                'sgm',
                'sgm.segment_id = s.id'
            )
            ->leftJoin(
                's',
                self::TABLE_SEGMENT_USER_MAP,
                'sum',
                'sum.segment_id = s.id'
            )
            ->andWhere(
                $selectQuery->expr()->eq(
                    $this->connection->quoteIdentifier('sum.user_id'),
                    ':user_id'
                )
            )
            ->setParameter(':user_id', $userId, ParameterType::INTEGER);

        return $selectQuery->execute()->fetchAllAssociative();
    }

    public function loadSegmentGroupById(int $id): array
    {
        $selectQuery = $this->buildFindSingleSegmentGroupQuery();

        $selectQuery
            ->andWhere(
                $selectQuery->expr()->eq(
                    $this->connection->quoteIdentifier('id'),
                    ':segment_group_id'
                )
            )
            ->setParameter(':segment_group_id', $id, ParameterType::INTEGER);

        $data = $selectQuery->execute()->fetchAssociative();

        if (false === $data) {
            throw new SegmentGroupNotFoundException("segmentGroupId: $id");
        }

        return $data;
    }

    public function loadSegmentGroupByIdentifier(string $identifier): array
    {
        $selectQuery = $this->buildFindSingleSegmentGroupQuery();

        $selectQuery
            ->andWhere(
                $selectQuery->expr()->eq(
                    $this->connection->quoteIdentifier('identifier'),
                    ':segment_group_identifier'
                )
            )
            ->setParameter(':segment_group_identifier', $identifier, ParameterType::STRING);

        $data = $selectQuery->execute()->fetchAssociative();

        if (false === $data) {
            throw new SegmentGroupNotFoundException("segmentGroupIdentifier: $identifier");
        }

        return $data;
    }

    private function buildFindSingleSegmentGroupQuery(): QueryBuilder
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery
            ->select(
                $this->connection->quoteIdentifier('id'),
                $this->connection->quoteIdentifier('identifier'),
                $this->connection->quoteIdentifier('name')
            )
            ->from($this->connection->quoteIdentifier(self::TABLE_SEGMENT_GROUPS));

        return $selectQuery;
    }

    public function insertSegmentGroup(string $identifier, string $name): int
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_SEGMENT_GROUPS))
            ->values([
                'identifier' => ':identifier',
                'name' => ':name',
            ])
            ->setParameters([
                ':identifier' => $identifier,
                ':name' => $name,
            ]);

        $query->execute();

        return (int)$this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_SEGMENT_GROUPS, 'id')
        );
    }

    public function updateSegmentGroup(int $segmentGroupId, string $identifier, string $name): void
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery
            ->update(self::TABLE_SEGMENT_GROUPS)
            ->set(
                $this->connection->quoteIdentifier('identifier'),
                ':identifier'
            )
            ->set(
                $this->connection->quoteIdentifier('name'),
                ':name'
            )
            ->andWhere(
                $selectQuery->expr()->eq(
                    $this->connection->quoteIdentifier('id'),
                    ':group_id'
                )
            )
            ->setParameter(':group_id', $segmentGroupId, ParameterType::INTEGER)
            ->setParameter(':identifier', $identifier, ParameterType::STRING)
            ->setParameter(':name', $name, ParameterType::STRING);

        $selectQuery->execute();
    }

    public function removeSegmentGroup(int $segmentGroupId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_SEGMENT_GROUPS))
            ->andWhere('id = :group_id')
            ->setParameter(':group_id', $segmentGroupId, ParameterType::INTEGER);

        $query->execute();
    }

    public function loadSegmentGroups(): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select(
            $this->connection->quoteIdentifier('id'),
            $this->connection->quoteIdentifier('identifier'),
            $this->connection->quoteIdentifier('name')
        )
            ->from($this->connection->quoteIdentifier(self::TABLE_SEGMENT_GROUPS));

        return $selectQuery->execute()->fetchAllAssociative();
    }

    public function assignSegmentToGroup(int $segmentId, int $segmentGroupId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_SEGMENT_GROUP_MAP))
            ->values([
                'segment_id' => ':segment_id',
                'group_id' => ':group_id',
            ])
            ->setParameters([
                ':segment_id' => $segmentId,
                ':group_id' => $segmentGroupId,
            ]);

        $query->execute();
    }

    public function unassignSegmentFromGroup(int $segmentId, int $segmentGroupId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_SEGMENT_GROUP_MAP))
            ->andWhere('segment_id = :segment_id')->andWhere('group_id = :group_id')
            ->setParameters([
                ':segment_id' => $segmentId,
                ':group_id' => $segmentGroupId,
            ]);

        $query->execute();
    }

    public function isUserAssignedToSegment(int $userId, int $segmentId): bool
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select('COUNT(s.id)')
            ->from($this->connection->quoteIdentifier(self::TABLE_SEGMENTS), 's')
            ->leftJoin(
                's',
                self::TABLE_SEGMENT_USER_MAP,
                'sum',
                'sum.segment_id = s.id'
            )
            ->andWhere(
                $selectQuery->expr()->eq(
                    $this->connection->quoteIdentifier('sum.user_id'),
                    ':user_id'
                )
            )
            ->andWhere(
                $selectQuery->expr()->eq(
                    $this->connection->quoteIdentifier('s.id'),
                    ':segment_id'
                )
            )
            ->setParameters([
                ':user_id' => $userId,
                ':segment_id' => $segmentId,
            ]);

        return $selectQuery->execute()->fetchOne() > 0;
    }

    public function assignUserToSegment(int $userId, int $segmentId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_SEGMENT_USER_MAP))
            ->values([
                'segment_id' => ':segment_id',
                'user_id' => ':user_id',
            ])
            ->setParameters([
                ':segment_id' => $segmentId,
                ':user_id' => $userId,
            ]);

        $query->execute();
    }

    public function unassignUserFromSegment(int $userId, int $segmentId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_SEGMENT_USER_MAP))
            ->andWhere('user_id = :user_id')->andWhere('segment_id = :segment_id')
            ->setParameters([
                ':user_id' => $userId,
                ':segment_id' => $segmentId,
            ]);

        $query->execute();
    }

    public function unassignUsersFromSegment(int $segmentId): void
    {
        $this->connection->delete(
            self::TABLE_SEGMENT_USER_MAP,
            [
                'segment_id' => $segmentId,
            ],
        );
    }

    protected function getSequenceName(string $table, string $column): string
    {
        return sprintf('%s_%s_seq', $table, $column);
    }
}

class_alias(DoctrineGateway::class, 'Ibexa\Platform\Segmentation\Persistence\Gateway\DoctrineGateway');
