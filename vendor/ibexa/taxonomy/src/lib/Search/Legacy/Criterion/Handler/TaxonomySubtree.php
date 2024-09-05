<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Search\Legacy\Criterion\Handler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as SearchCriterion;
use Ibexa\Contracts\Taxonomy\Search\Query\Criterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntryAssignment;
use LogicException;

final class TaxonomySubtree extends CriterionHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(Connection $connection, EntityManagerInterface $entityManager)
    {
        parent::__construct($connection);

        $this->entityManager = $entityManager;
    }

    public function accept(SearchCriterion $criterion): bool
    {
        return $criterion instanceof Criterion\TaxonomySubtree;
    }

    /**
     * @param array<string, mixed> $languageSettings
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        SearchCriterion $criterion,
        array $languageSettings
    ) {
        if (!is_array($criterion->value) || empty($criterion->value)) {
            throw new LogicException('Int in array expected.');
        }

        $entryId = reset($criterion->value);
        $subSelect = $this->connection->createQueryBuilder();
        $entryAssignmentClassMetadata = $this->entityManager->getClassMetadata(TaxonomyEntryAssignment::class);
        $entryClassMetadata = $this->entityManager->getClassMetadata(TaxonomyEntry::class);

        $subSelectLeft = $this->connection->createQueryBuilder();
        $subSelectRight = $this->connection->createQueryBuilder();

        $subSelectLeft
            ->select(
                't.left',
            )->from(
                $entryClassMetadata->getTableName(),
                't',
            )->where($queryBuilder->expr()->eq(
                't.id',
                $queryBuilder->createNamedParameter($entryId, ParameterType::INTEGER),
            ));

        $subSelectRight
            ->select(
                't.right',
            )->from(
                $entryClassMetadata->getTableName(),
                't',
            )->where($queryBuilder->expr()->eq(
                't.id',
                $queryBuilder->createNamedParameter($entryId, ParameterType::INTEGER),
            ));

        $subSelect
            ->select(
                'ta.content_id',
            )
            ->from(
                $entryAssignmentClassMetadata->getTableName(),
                'ta',
            )
            // Below condition is necessary to prevent performance degradation with increasing versions of same content
            ->join(
                'ta',
                'ezcontentobject',
                'c',
                (string)$queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq(
                        'c.id',
                        'ta.content_id',
                    ),
                    $queryBuilder->expr()->eq(
                        'c.current_version',
                        'ta.version_no',
                    ),
                )
            )
            ->leftJoin(
                'ta',
                $entryClassMetadata->getTableName(),
                't',
                $queryBuilder->expr()->eq(
                    't.id',
                    'ta.entry_id',
                )
            )
            ->andWhere(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->gte(
                        't.left',
                        '(' . $subSelectLeft->getSQL() . ')',
                    ),
                    $queryBuilder->expr()->lte(
                        't.right',
                        '(' . $subSelectRight->getSQL() . ')',
                    ),
                )
            );

        return $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL()
        );
    }
}
