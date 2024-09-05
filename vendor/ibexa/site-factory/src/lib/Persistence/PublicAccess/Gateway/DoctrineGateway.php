<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Persistence\PublicAccess\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\SiteFactory\Values\Query\Criterion;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\SiteFactory\Exception\DataReadException;
use Ibexa\SiteFactory\Persistence\Site\Handler\HandlerInterface;
use const PHP_INT_MAX;

class DoctrineGateway extends AbstractGateway
{
    public const TABLE_PUBLIC_ACCESS = 'ezsite_public_access';

    public const COLUMN_ID = 'public_access_identifier';

    /** @var \Doctrine\DBAL\Connection */
    protected $connection;

    /** @var \Ibexa\SiteFactory\Persistence\Site\Handler\HandlerInterface */
    private $siteHandler;

    /**
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(
        Connection $connection,
        HandlerInterface $siteHandler
    ) {
        $this->connection = $connection;
        $this->siteHandler = $siteHandler;
    }

    /**
     * {@inheritdoc}
     */
    protected function doCount(Criterion $criterion, Site $site = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select('COUNT(' . self::COLUMN_ID . ')')
            ->from(self::TABLE_PUBLIC_ACCESS);

        if ($site !== null) {
            $query->where($query->expr()->eq('site_id', ':siteId'))
            ->setParameter(':siteId', $site->id, ParameterType::INTEGER);
        }

        return (int) $query->execute()->fetchColumn();
    }

    private function createSelectQuery(): QueryBuilder
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery
            ->select(
                's.' . self::COLUMN_ID,
                's.site_id',
                's.site_access_group',
                's.status',
                's.config',
                's.site_matcher_host',
                's.site_matcher_path',
            )
            ->from(self::TABLE_PUBLIC_ACCESS, 's')
        ;

        return $selectQuery;
    }

    public function loadPublicAccessData(string $identifier): ?array
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select(
                's.' . self::COLUMN_ID,
                's.site_id',
                's.site_access_group',
                's.status',
                's.config',
                's.site_matcher_host',
                's.site_matcher_path',
            )
            ->where($query->expr()->eq('s.' . self::COLUMN_ID, ':identifier'))
            ->from(self::TABLE_PUBLIC_ACCESS, 's')
            ->setParameter(':identifier', $identifier, ParameterType::STRING)
        ;

        try {
            $result = $query->execute()->fetch(FetchMode::ASSOCIATIVE);
        } catch (TableNotFoundException | ConnectionException  $e) {
            throw new DataReadException();
        }

        if ($result === false) {
            return null;
        }

        return $result;
    }

    public function find(Criterion $criterion, int $offset = 0, int $limit = -1, bool $doCount = true): array
    {
        $count = $doCount ? $this->doCount($criterion) : null;
        if (!$doCount && $limit === 0) {
            throw new \RuntimeException('Invalid query. Cannot disable count and request 0 items at the same time');
        }

        if ($limit === 0 || ($count !== null && $count <= $offset)) {
            return [
                'count' => $count,
                'rows' => [],
            ];
        }
        $limit = $limit > 0 ? $limit : PHP_INT_MAX;

        $query = $this->createSelectQuery();
        $query
            ->orderBy(self::COLUMN_ID, 'DESC');

        $statement = $query->execute();

        return [
            'count' => $count,
            'rows' => $statement->fetchAll(FetchMode::ASSOCIATIVE),
        ];
    }

    public function match(string $host): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $query = $queryBuilder->select('pa.public_access_identifier', 'pa.site_id', 'pa.site_matcher_path')
            ->from(self::TABLE_PUBLIC_ACCESS, 'pa')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('pa.status', PublicAccess::STATUS_ONLINE),
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq('pa.site_matcher_host', ':host'),
                        $queryBuilder->expr()->isNull('pa.site_matcher_host')
                    ),
                )
            )
            ->setParameter('host', $host);

        return $query->execute()->fetchAll();
    }
}

class_alias(DoctrineGateway::class, 'EzSystems\EzPlatformSiteFactory\Persistence\PublicAccess\Gateway\DoctrineGateway');
