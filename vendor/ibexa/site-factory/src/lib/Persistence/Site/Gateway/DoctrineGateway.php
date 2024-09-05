<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Persistence\Site\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\SiteFactory\Values\Query\Criterion;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;
use Ibexa\SiteFactory\Persistence\Site\Query\CriteriaConverter;
use PDO;
use const PHP_INT_MAX;

class DoctrineGateway extends AbstractGateway
{
    private const TABLE_SITE = 'ezsite';
    private const TABLE_PUBLIC_ACCESS = 'ezsite_public_access';

    private const COLUMN_ID = 'id';

    /** @var \Doctrine\DBAL\Connection */
    protected $connection;

    /** @var \Ibexa\SiteFactory\Persistence\Site\Query\CriteriaConverter */
    private $criteriaConverter;

    /**
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Ibexa\SiteFactory\Persistence\Site\Query\CriteriaConverter $criteriaConverter
     */
    public function __construct(
        Connection $connection,
        CriteriaConverter $criteriaConverter
    ) {
        $this->connection = $connection;
        $this->criteriaConverter = $criteriaConverter;
    }

    public function find(Criterion $criterion, $offset, $limit, $doCount = true): array
    {
        $count = $doCount ? $this->doCount($criterion) : null;

        if (!$doCount && $limit === 0) {
            throw new \RuntimeException('Invalid query. Cannot disable count and request 0 items at the same time');
        }

        if ($count !== null && $count <= $offset) {
            return [
                'count' => $count,
                'rows' => [],
            ];
        }
        $limit = $limit > 0 ? $limit : PHP_INT_MAX;

        $subQuery = $this->connection->createQueryBuilder();
        $subQuery
            ->select('*')
            ->from(self::TABLE_SITE)
            ->where($this->criteriaConverter->convertCriteria($subQuery, $criterion))
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy(self::COLUMN_ID, 'DESC');

        $query = $this->connection->createQueryBuilder();
        $expr = $query->expr();
        $query
            ->select('sg.*', 's.*')
            ->from(sprintf('(%s)', $subQuery->getSQL()), 'sg')
            ->leftJoin('sg', self::TABLE_PUBLIC_ACCESS, 's', $expr->eq('s.site_id', 'sg.id'))
            ->setParameters($subQuery->getParameters());

        $statement = $query->execute();

        return [
            'count' => $count,
            'rows' => $statement->fetchAll(FetchMode::ASSOCIATIVE),
        ];
    }

    public function count(Criterion $criterion): int
    {
        return $this->doCount($criterion);
    }

    public function insert(SiteCreateStruct $siteCreateStruct): int
    {
        $query = $this->connection->createQueryBuilder();
        $siteGroupId = $this->insertSite($siteCreateStruct);
        foreach ($siteCreateStruct->publicAccesses as $publicAccess) {
            $query
                ->insert(self::TABLE_PUBLIC_ACCESS)
                ->values([
                    'public_access_identifier' => ':public_access_identifier',
                    'status' => ':status',
                    'site_id' => ':site_id',
                    'site_access_group' => ':site_access_group',
                    'config' => ':config',
                    'site_matcher_host' => ':site_matcher_host',
                    'site_matcher_path' => ':site_matcher_path',
                ])
                ->setParameters([
                    ':public_access_identifier' => $publicAccess->identifier,
                    ':status' => $publicAccess->status,
                    ':site_id' => $siteGroupId,
                    ':site_access_group' => $publicAccess->saGroup,
                    ':config' => $publicAccess->config,
                    ':site_matcher_host' => $publicAccess->matcherConfiguration->host,
                    ':site_matcher_path' => $publicAccess->matcherConfiguration->path,
                ]);

            $query->execute();
        }

        return $siteGroupId;
    }

    protected function doCount(Criterion $criterion): int
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select('COUNT(' . self::COLUMN_ID . ')')
            ->from(self::TABLE_SITE)
            ->where($this->criteriaConverter->convertCriteria($query, $criterion));

        return (int) $query->execute()->fetchColumn();
    }

    private function createSelectQuery(): QueryBuilder
    {
        $selectQuery = $this->connection->createQueryBuilder();
        $expr = $selectQuery->expr();
        $selectQuery
            ->select(
                'sg.*',
                's.*',
            )
            ->leftJoin('sg', self::TABLE_PUBLIC_ACCESS, 's', $expr->eq('s.site_id', 'sg.id'))
            ->from(self::TABLE_SITE, 'sg')
        ;

        return $selectQuery;
    }

    public function loadSiteData(int $id): array
    {
        $query = $this->createSelectQuery();
        $query
            ->where($query->expr()->eq('sg.id', ':id'))
            ->setParameter(':id', $id, ParameterType::INTEGER)
        ;

        return  $query->execute()->fetchAll(FetchMode::ASSOCIATIVE);
    }

    public function insertSite(SiteCreateStruct $siteCreateStruct): int
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert(self::TABLE_SITE)
            ->values([
                'name' => ':name',
                'created' => ':created',
            ])
            ->setParameters([
                ':name' => $siteCreateStruct->siteName,
                ':created' => $siteCreateStruct->siteCreated->getTimestamp(),
            ]);

        $query->execute();

        return (int) $this->connection->lastInsertId();
    }

    public function deleteSite(int $id): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete(self::TABLE_PUBLIC_ACCESS)
            ->where($query->expr()->eq('site_id', ':id'))
            ->setParameter(':id', $id, PDO::PARAM_INT);

        $query->execute();

        $query
            ->delete(self::TABLE_SITE)
            ->where($query->expr()->eq('id', ':id'))
            ->setParameter(':id', $id, PDO::PARAM_INT);

        $query->execute();
    }

    public function canDeleteSite(int $id): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select('COUNT(site_id)')
            ->from(self::TABLE_PUBLIC_ACCESS)
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('site_id', ':id'),
                    $query->expr()->neq('status', '0'),
                )
            )
            ->setParameter(':id', $id, PDO::PARAM_INT);

        return (int)$query->execute()->fetchColumn() === 0;
    }

    public function update(int $siteId, SiteUpdateStruct $siteUpdateStruct): void
    {
        $this->updateSite($siteId, $siteUpdateStruct);

        $publicAccesses = array_column(
            $siteUpdateStruct->publicAccesses,
            'identifier'
        );

        // removing deleted publicAccesses
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete(self::TABLE_PUBLIC_ACCESS)
            ->where($query->expr()->eq('site_id', ':id'))
            ->andWhere($query->expr()->notIn('public_access_identifier', ':identifiers'))
            ->setParameter(':id', $siteId, PDO::PARAM_INT)
            ->setParameter(':identifiers', $publicAccesses, Connection::PARAM_STR_ARRAY);
        $query->execute();
        // removing deleted publicAccesses

        // find publicAccesses to add
        $query = $this->connection->createQueryBuilder();
        $query
            ->select('public_access_identifier')
            ->from(self::TABLE_PUBLIC_ACCESS)
            ->where($query->expr()->eq('site_id', ':site_id'), )
            ->setParameters([
                ':site_id' => $siteId,
            ]);

        $statement = $query->execute();
        $existingPublicAccessIdentifiers = $statement->fetchAll(FetchMode::COLUMN);
        $newPublicAccesses = array_diff($publicAccesses, $existingPublicAccessIdentifiers);
        // find publicAccesses to add

        foreach ($siteUpdateStruct->publicAccesses as $publicAccess) {
            $query = $this->connection->createQueryBuilder();
            if (in_array($publicAccess->identifier, $newPublicAccesses)) {
                $query
                    ->insert(self::TABLE_PUBLIC_ACCESS)
                    ->values([
                        'public_access_identifier' => ':public_access_identifier',
                        'status' => ':status',
                        'site_id' => ':site_id',
                        'site_access_group' => ':site_access_group',
                        'config' => ':config',
                        'site_matcher_host' => ':site_matcher_host',
                        'site_matcher_path' => ':site_matcher_path',
                    ])
                    ->setParameters([
                        ':public_access_identifier' => $publicAccess->identifier,
                        ':status' => $publicAccess->status,
                        ':site_id' => $siteId,
                        ':site_access_group' => $publicAccess->saGroup,
                        ':config' => $publicAccess->config,
                        ':site_matcher_host' => $publicAccess->matcherConfiguration->host,
                        ':site_matcher_path' => $publicAccess->matcherConfiguration->path,
                    ]);

                $query->execute();
            } else {
                $query
                    ->update(self::TABLE_PUBLIC_ACCESS)
                    ->set('status', ':status')
                    ->set('site_access_group', ':site_access_group')
                    ->set('config', ':config')
                    ->set('site_matcher_host', ':site_matcher_host')
                    ->set('site_matcher_path', ':site_matcher_path')
                    ->where(
                        $query->expr()->andX(
                            $query->expr()->eq('site_id', ':site_id'),
                            $query->expr()->eq('public_access_identifier', ':public_access_identifier'),
                        )
                    )
                    ->setParameters([
                        ':status' => $publicAccess->status,
                        ':site_access_group' => $publicAccess->saGroup,
                        ':config' => $publicAccess->config,
                        ':site_matcher_host' => $publicAccess->matcherConfiguration->host,
                        ':site_matcher_path' => $publicAccess->matcherConfiguration->path,
                        ':site_id' => $siteId,
                        ':public_access_identifier' => $publicAccess->identifier,
                    ]);

                $query->execute();
            }
        }
    }

    public function updateSite(int $siteId, SiteUpdateStruct $siteUpdateStruct): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update(self::TABLE_SITE)
            ->set('name', ':name')
            ->where('id = :id')
            ->setParameter(':id', $siteId, PDO::PARAM_STR)
            ->setParameter(':name', $siteUpdateStruct->siteName, PDO::PARAM_STR);

        $query->execute();
    }
}

class_alias(DoctrineGateway::class, 'EzSystems\EzPlatformSiteFactory\Persistence\Site\Gateway\DoctrineGateway');
