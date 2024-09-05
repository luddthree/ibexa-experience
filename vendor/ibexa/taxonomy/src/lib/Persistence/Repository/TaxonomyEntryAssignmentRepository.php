<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Persistence\Repository;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends \Doctrine\ORM\EntityRepository<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntryAssignment>
 */
class TaxonomyEntryAssignmentRepository extends EntityRepository
{
    /**
     * @return array<int>
     */
    public function findAssignedEntries(int $contentId, int $versionNo, string $taxonomy): array
    {
        $queryBuilder = $this->getAssignedEntriesQueryBuilder($contentId, $versionNo, $taxonomy);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return array<int>
     */
    public function getAssignedEntryIds(int $contentId, int $versionNo, string $taxonomy): array
    {
        $queryBuilder = $this->getAssignedEntriesQueryBuilder($contentId, $versionNo, $taxonomy);
        $queryBuilder->select('te.id');

        return $queryBuilder->getQuery()->getSingleColumnResult();
    }

    /**
     * @param array<int> $entriesToUnassign
     */
    public function deleteContentAssignments(int $contentId, int $versionNo, array $entriesToUnassign): void
    {
        $deleteQueryBuilder = $this->createQueryBuilder('tea');
        $deleteQueryBuilder
            ->delete()
            ->andWhere($deleteQueryBuilder->expr()->eq('tea.content', ':contentId'))
            ->andWhere($deleteQueryBuilder->expr()->eq('tea.versionNo', ':versionNo'))
            ->andWhere($deleteQueryBuilder->expr()->in('tea.entry', ':entryIds'));

        $deleteQuery = $deleteQueryBuilder->getQuery();

        $deleteQuery->execute([
            'contentId' => $contentId,
            'versionNo' => $versionNo,
            'entryIds' => $entriesToUnassign,
        ]);
    }

    private function getAssignedEntriesQueryBuilder(int $contentId, int $versionNo, string $taxonomy): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('tea');
        $queryBuilder
            ->join('tea.entry', 'te')
            ->andWhere(
                $queryBuilder->expr()->eq('tea.content', ':contentId'),
            )
            ->andWhere(
                $queryBuilder->expr()->eq('tea.versionNo', ':versionNo')
            )
            ->andWhere(
                $queryBuilder->expr()->eq('te.taxonomy', ':taxonomy')
            );

        $queryBuilder->setParameter('contentId', $contentId, Types::INTEGER);
        $queryBuilder->setParameter('versionNo', $versionNo, Types::INTEGER);
        $queryBuilder->setParameter('taxonomy', $taxonomy, Types::STRING);

        return $queryBuilder;
    }
}
