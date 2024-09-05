<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Search\Legacy\Criterion\Handler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as SearchCriterion;
use Ibexa\Contracts\Taxonomy\Search\Query\Criterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntryAssignment;

final class TaxonomyEntryId extends CriterionHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(Connection $connection, EntityManagerInterface $entityManager)
    {
        parent::__construct($connection);

        $this->entityManager = $entityManager;
    }

    public function accept(SearchCriterion $criterion): bool
    {
        return $criterion instanceof Criterion\TaxonomyEntryId;
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
        $entryIds = (array) $criterion->value;
        $subSelect = $this->connection->createQueryBuilder();
        $classMetadata = $this->entityManager->getClassMetadata(TaxonomyEntryAssignment::class);

        $contentIdColumn = $classMetadata->getColumnName('content');
        $versionNoColumn = $classMetadata->getColumnName('versionNo');
        $tagEntryIdColumn = $classMetadata->getSingleAssociationJoinColumnName('entry');

        $tagAssignmentAlias = 'ta';
        $contentAlias = 'c';

        $subSelect
            ->select(
                sprintf('%s.%s', $tagAssignmentAlias, $contentIdColumn),
            )
            ->from(
                $classMetadata->getTableName(),
                $tagAssignmentAlias,
            )
            // Below condition is necessary to prevent performance degradation with increasing versions of same content
            ->join(
                $tagAssignmentAlias,
                'ezcontentobject',
                $contentAlias,
                (string)$queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq(
                        sprintf('%s.id', $contentAlias),
                        sprintf('%s.%s', $tagAssignmentAlias, $contentIdColumn),
                    ),
                    $queryBuilder->expr()->eq(
                        sprintf('%s.current_version', $contentAlias),
                        sprintf('%s.%s', $tagAssignmentAlias, $versionNoColumn),
                    ),
                )
            )
            ->andWhere(
                $queryBuilder->expr()->in(
                    sprintf('%s.%s', $tagAssignmentAlias, $tagEntryIdColumn),
                    $queryBuilder->createNamedParameter($entryIds, Connection::PARAM_INT_ARRAY)
                )
            );

        return $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL()
        );
    }
}
