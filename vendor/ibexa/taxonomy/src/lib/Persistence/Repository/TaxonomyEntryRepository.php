<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Persistence\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;

/**
 * @extends \Gedmo\Tree\Entity\Repository\NestedTreeRepository<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry>
 */
class TaxonomyEntryRepository extends NestedTreeRepository implements ServiceEntityRepositoryInterface
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(
        EntityManagerInterface $em,
        TaxonomyConfiguration $taxonomyConfiguration
    ) {
        parent::__construct($em, $em->getClassMetadata(TaxonomyEntry::class));
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public function findById(int $id): ?TaxonomyEntry
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findByContentId(int $contentId): ?TaxonomyEntry
    {
        return $this->findOneBy(['contentId' => $contentId]);
    }

    public function findByIdentifier(string $identifier, string $taxonomy = null): ?TaxonomyEntry
    {
        $taxonomy ??= $this->taxonomyConfiguration->getDefaultTaxonomyName();

        return $this->findOneBy([
            'identifier' => $identifier,
            'taxonomy' => $taxonomy,
        ]);
    }

    /**
     * @return array<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry>
     */
    public function findByMatchingName(
        string $name,
        ?string $languageCode = null,
        ?int $limit = null,
        int $offset = 0,
        string $taxonomy = null
    ): array {
        $taxonomy ??= $this->taxonomyConfiguration->getDefaultTaxonomyName();

        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->andWhere('t.taxonomy = :taxonomy')
            ->setParameter('taxonomy', $taxonomy, ParameterType::STRING);

        if (null !== $languageCode) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like(
                    (string) $queryBuilder->expr()->lower('t.names'),
                    (string) $queryBuilder->expr()->lower(':query'),
                )
            );
        } else {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like(
                    (string) $queryBuilder->expr()->lower('t.name'),
                    (string) $queryBuilder->expr()->lower(':query'),
                )
            );
        }

        $queryBuilder
            ->setParameter(
                'query',
                str_replace(
                    '*',
                    '%',
                    addcslashes($name, '%_')
                )
            );

        if ($limit !== null) {
            $queryBuilder->setMaxResults($limit);
        }
        $queryBuilder->setFirstResult($offset);

        return $queryBuilder->getQuery()->execute();
    }

    public function countAllEntries(?string $taxonomyName = null): int
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->select(
            $this->_em->getConnection()->getDatabasePlatform()->getCountExpression('t.id')
        );

        if (null !== $taxonomyName) {
            $queryBuilder->where($queryBuilder->expr()->eq('t.taxonomy', ':taxonomyName'));
            $queryBuilder->setParameter('taxonomyName', $taxonomyName, ParameterType::STRING);
        }

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @return array<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry>
     */
    public function loadAllEntries(
        ?string $taxonomyName,
        int $limit,
        int $offset
    ): array {
        $queryBuilder = $this->createQueryBuilder('t');

        if (null !== $taxonomyName) {
            $queryBuilder->where($queryBuilder->expr()->eq('t.taxonomy', ':taxonomyName'));
            $queryBuilder->setParameter('taxonomyName', $taxonomyName, ParameterType::STRING);
        }

        $queryBuilder->setMaxResults($limit);
        $queryBuilder->setFirstResult($offset);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @return array<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry>
     */
    public function loadEntryChildren(int $parentEntryId, ?int $limit = 30, int $offset = 0): array
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder
            ->select()
            ->where($queryBuilder->expr()->eq('t.parent', ':parentId'))
            ->setParameter('parentId', $parentEntryId, ParameterType::INTEGER);

        if ($limit !== null) {
            $queryBuilder->setMaxResults($limit);
        }

        $queryBuilder->setFirstResult($offset);

        return $queryBuilder->getQuery()->execute();
    }

    public function countEntryChildren(int $parentEntryId): int
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder
            ->select(
                $this->_em->getConnection()->getDatabasePlatform()->getCountExpression('t.id')
            )
            ->where($queryBuilder->expr()->eq('t.parent', ':parentId'))
            ->setParameter('parentId', $parentEntryId, ParameterType::INTEGER);

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
