<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Persistence\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\FieldTypePage\FieldType\Page\Storage\DoctrineGateway;
use Ibexa\FieldTypePage\Persistence\EntriesHandlerInterface;
use Ibexa\FieldTypePage\Persistence\EntriesMapperInterface;

class BlockVisibilityEntriesHandler implements EntriesHandlerInterface
{
    public const TABLE_CONTENT_OBJECT = 'ezcontentobject';
    public const TABLE_CONTENT_OBJECT_VERSION = 'ezcontentobject_version';

    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    /** @var \Doctrine\DBAL\Platforms\AbstractPlatform|null */
    private $dbPlatform;

    /** @var \Ibexa\FieldTypePage\Persistence\BlockEntriesMapper */
    private $entriesMapper;

    /** @var string */
    private $dateColumnName;

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct(Connection $connection, EntriesMapperInterface $entriesMapper, string $dateColumnName)
    {
        $this->connection = $connection;
        $this->entriesMapper = $entriesMapper;
        $this->dateColumnName = $dateColumnName;
    }

    private function getDbPlatform()
    {
        if (null === $this->dbPlatform) {
            $this->dbPlatform = $this->connection->getDatabasePlatform();
        }

        return $this->dbPlatform;
    }

    /**
     * @return \Ibexa\FieldTypePage\Persistence\BlockEntry[]
     */
    public function getEntriesByIds(array $scheduleEntriesIds): iterable
    {
        $query = $this->getSelectQuery();
        $query->where(
            $query->expr()->in('b.id', ':ids')
        );
        $query->orderBy('id', 'ASC')
            ->setParameter('ids', $scheduleEntriesIds, Connection::PARAM_INT_ARRAY);

        $statement = $query->execute();
        $rows = $statement->fetchAllAssociative();

        return $this->entriesMapper->map($rows);
    }

    /**
     * @return \Ibexa\FieldTypePage\Persistence\BlockEntry[]
     */
    public function getVersionsEntriesByDateRange(
        int $start,
        int $end,
        array $languages = [],
        ?int $sinceId = null,
        int $limit = 25
    ): iterable {
        $query = $this->getSelectQueryForEntriesInDateRangeCriteria(
            $this->getSelectQuery(),
            $start,
            $end,
            $languages,
            $sinceId
        );

        return $this->getPaginatedResult(0, $limit, $query);
    }

    public function countVersionsEntries(): int
    {
        $query = $this->getCountQuery();

        $statement = $query->execute();
        $versionCount = $statement->fetchAllAssociative();

        return (int)$versionCount['count'];
    }

    /**
     * @param int[] $languages
     */
    public function countVersionsEntriesInDateRange(
        int $start,
        int $end,
        array $languages = [],
        ?int $sinceId = null
    ): int {
        $query = $this->getSelectQueryForEntriesInDateRangeCriteria(
            $this->getCountQuery(),
            $start,
            $end,
            $languages,
            $sinceId
        );

        $statement = $query->execute();

        return (int)$statement->fetchOne();
    }

    private function getCountQuery(): QueryBuilder
    {
        $query = $this->createQueryForBlockInPublishedVersion();
        $query->select(
            sprintf('%s AS count', $this->getDbPlatform()->getCountExpression('b.id'))
        );

        return $query;
    }

    private function getSelectQuery(): QueryBuilder
    {
        $query = $this->createQueryForBlockInPublishedVersion();
        $query->select(
            'b.id',
            'b.name as block_name',
            'b.type as block_type',
            'co.owner_id as user_id',
            'cov.contentobject_id as content_id',
            'cov.id as version_id',
            'cov.version as version_number',
        );
        $this->addSelectDateColumn($query);

        return $query;
    }

    private function createQueryForBlockInPublishedVersion(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->from($this->connection->quoteIdentifier(DoctrineGateway::TABLE_BLOCKS), 'b')
            ->leftJoin(
                'b',
                $this->connection->quoteIdentifier(DoctrineGateway::TABLE_MAP_BLOCKS_ZONES),
                'mbz',
                'mbz.block_id = b.id'
            )
            ->leftJoin(
                'b',
                $this->connection->quoteIdentifier(DoctrineGateway::TABLE_MAP_ZONES_PAGES),
                'mzp',
                'mzp.zone_id = mbz.zone_id'
            )
            ->leftJoin(
                'b',
                $this->connection->quoteIdentifier(DoctrineGateway::TABLE_BLOCKS_VISIBILITY),
                'bv',
                'bv.block_id = b.id'
            )
            ->leftJoin(
                'mzp',
                $this->connection->quoteIdentifier(DoctrineGateway::TABLE_PAGES),
                'p',
                'mzp.page_id = p.id'
            )
            ->leftJoin(
                'p',
                $this->connection->quoteIdentifier(self::TABLE_CONTENT_OBJECT_VERSION),
                'cov',
                $query->expr()->and(
                    'p.content_id = cov.contentobject_id',
                    'p.version_no = cov.version'
                )
            )
            ->leftJoin(
                'p',
                $this->connection->quoteIdentifier(self::TABLE_CONTENT_OBJECT),
                'co',
                'p.content_id = co.id'
            )
            ->andWhere('cov.status = :status')
            ->setParameter(':status', VersionInfo::STATUS_PUBLISHED, ParameterType::INTEGER);

        return $query;
    }

    /**
     * @param int[] $languages
     */
    private function getSelectQueryForEntriesInDateRangeCriteria(
        QueryBuilder $query,
        int $start,
        int $end,
        array $languages = [],
        ?int $sinceId = null
    ): QueryBuilder {
        $expr = $query->expr();

        $criteria = [];
        $criteria[] = $expr->gte(
            $this->dateColumnName,
            $query->createNamedParameter($start, ParameterType::INTEGER)
        );

        $criteria[] = $expr->lt(
            $this->dateColumnName,
            $query->createNamedParameter($end, ParameterType::INTEGER)
        );

        if (!empty($languages)) {
            $criteria[] = $expr->in(
                'cov.initial_language_id',
                $query->createNamedParameter($languages, Connection::PARAM_INT_ARRAY)
            );
        }

        if ($sinceId !== null) {
            $criteria[] = $expr->gt(
                'b.id',
                $query->createNamedParameter($sinceId, ParameterType::INTEGER)
            );
        }

        $query->andWhere($expr->and(...$criteria));

        return $query;
    }

    /**
     * @return \Ibexa\FieldTypePage\Persistence\BlockEntry[]
     */
    private function getPaginatedResult(
        int $page,
        ?int $limit,
        QueryBuilder $query
    ): iterable {
        $query->addOrderBy('action_timestamp', 'ASC');
        $query->addOrderBy('b.id', 'ASC');

        if (null !== $limit) {
            $query->setMaxResults($limit);
            $query->setFirstResult($limit * $page);
        }

        $statement = $query->execute();
        $scheduledVersions = $statement->fetchAllAssociative();

        return $this->entriesMapper->map($scheduledVersions);
    }

    private function addSelectDateColumn(QueryBuilder $query): void
    {
        $dateSelect = "{$this->dateColumnName} as action_timestamp";
        $query->addSelect($dateSelect);
    }
}

class_alias(BlockVisibilityEntriesHandler::class, 'EzSystems\EzPlatformPageFieldType\Persistence\Gateway\BlockVisibilityEntriesHandler');
