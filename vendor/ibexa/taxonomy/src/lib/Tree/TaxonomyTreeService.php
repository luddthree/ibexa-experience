<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Tree;

use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\AbstractQuery;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Taxonomy\Exception\TaxonomyNotFoundException;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository;
use Ibexa\Taxonomy\Security\ValueObject\TaxonomyValue;

/**
 * @internal
 */
final class TaxonomyTreeService implements TaxonomyTreeServiceInterface
{
    private TaxonomyEntryRepository $taxonomyEntryRepository;

    private TreeNodeMapper $treeNodeMapper;

    private PermissionResolver $permissionResolver;

    private LanguageService $languageService;

    public function __construct(
        TaxonomyEntryRepository $taxonomyEntryRepository,
        TreeNodeMapper $treeNodeMapper,
        PermissionResolver $permissionResolver,
        LanguageService $languageService
    ) {
        $this->taxonomyEntryRepository = $taxonomyEntryRepository;
        $this->treeNodeMapper = $treeNodeMapper;
        $this->permissionResolver = $permissionResolver;
        $this->languageService = $languageService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function loadTreeRoot(string $taxonomyName): array
    {
        $queryBuilder = $this->taxonomyEntryRepository->getNodesHierarchyQueryBuilder(null, true);
        $queryBuilder->andWhere($queryBuilder->expr()->eq('node.taxonomy', ':taxonomyName'));
        $queryBuilder->setParameter('taxonomyName', $taxonomyName, ParameterType::STRING);

        $rootNode = $queryBuilder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

        if ($rootNode === null) {
            throw TaxonomyNotFoundException::createWithTaxonomyName($taxonomyName);
        }

        $taxonomyEntryLimitationValueObject = new TaxonomyValue(['taxonomy' => $taxonomyName]);
        if (!$this->permissionResolver->canUser('taxonomy', 'read', $taxonomyEntryLimitationValueObject)) {
            throw new UnauthorizedException(
                'taxonomy',
                'read',
                ['entryIdentifier' => $rootNode['name']],
            );
        }

        return $this->taxonomyEntryRepository->buildTreeArray([$rootNode]);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function loadNode(int $nodeId): array
    {
        /** @var \Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry $node */
        $node = $this->taxonomyEntryRepository->findOneBy(['id' => $nodeId]);

        // Checking single parent node is enough, we don't need to filter children nodes as they have the same taxonomy
        if (!$this->permissionResolver->canUser('taxonomy', 'read', $node)) {
            throw new UnauthorizedException(
                'taxonomy',
                'read',
                ['entryIdentifier' => $node->getName()],
            );
        }

        /** @var array<array<string, mixed>> $nodes */
        $nodes = $this->taxonomyEntryRepository->childrenHierarchy(
            $node,
            true,
            [],
            true
        );

        return reset($nodes) ?: [];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function loadSubtree(array $nodeIds): array
    {
        $nodes = $this->taxonomyEntryRepository->findBy(['id' => $nodeIds]);

        $nodesInPath = [];
        foreach ($nodes as $node) {
            foreach ($this->taxonomyEntryRepository->getPath($node) as $nodeInPath) {
                if (!$this->permissionResolver->canUser('taxonomy', 'read', $nodeInPath)) {
                    continue;
                }

                $nodesInPath[$nodeInPath->getId()] = $nodeInPath;
            }
        }

        $allNodes = [];
        foreach ($nodesInPath as $node) {
            $query = $this->taxonomyEntryRepository->getChildrenQuery($node, true, null, 'ASC', true);
            foreach ($query->getArrayResult() as $child) {
                $allNodes[$child['id']] = $child;
            }
        }

        return $this->taxonomyEntryRepository->buildTreeArray(
            $this->sortNodes($allNodes)
        );
    }

    public function findNodes(
        string $query,
        ?string $languageCode = null,
        ?int $limit = self::DEFAULT_LIMIT,
        int $offset = 0,
        string $taxonomy = null
    ): array {
        // throws exception on invalid language
        if (null !== $languageCode) {
            $this->languageService->loadLanguage($languageCode);
        }

        $persistenceTaxonomyEntries = $this->taxonomyEntryRepository->findByMatchingName(
            sprintf('*%s*', $query),
            $languageCode,
            $limit,
            $offset,
            $taxonomy,
        );

        $persistenceTaxonomyEntries = $this->filterLanguages($persistenceTaxonomyEntries, $query, $languageCode);
        $persistenceTaxonomyEntries = $this->filterOnPermissions($persistenceTaxonomyEntries);

        $tree = [];
        foreach ($persistenceTaxonomyEntries as $node) {
            foreach ($this->taxonomyEntryRepository->getPath($node) as $nodeInPath) {
                $tree[$nodeInPath->getId()] = $nodeInPath;
            }
        }

        return $this->taxonomyEntryRepository->buildTreeArray(
            $this->sortNodes($this->mapToArrays($tree))
        );
    }

    public function countIndirectChildren(int $nodeId): int
    {
        $node = $this->taxonomyEntryRepository->findOneBy(['id' => $nodeId]);

        return $this->taxonomyEntryRepository->childCount($node, false);
    }

    /**
     * @param array<mixed> $nodes
     *
     * @return array<mixed>
     */
    private function sortNodes(array $nodes): array
    {
        uasort($nodes, static function (array $a, array $b): int {
            return $a['left'] <=> $b['left'];
        });

        return $nodes;
    }

    /**
     * @param array<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry> $entries
     *
     * @return array<mixed>
     */
    private function mapToArrays(array $entries): array
    {
        $mappedEntries = [];
        foreach ($entries as $entry) {
            $mappedEntries[] = $this->treeNodeMapper->mapToArray($entry);
        }

        return $mappedEntries;
    }

    /**
     * @param array<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry> $taxonomyEntries
     *
     * @return array<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry>
     */
    private function filterOnPermissions(array $taxonomyEntries): array
    {
        $filteredEntries = [];
        foreach ($taxonomyEntries as $entry) {
            if ($this->permissionResolver->canUser('taxonomy', 'read', $entry)) {
                $filteredEntries[] = $entry;
            }
        }

        return $filteredEntries;
    }

    /**
     * @param array<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry> $taxonomyEntries
     *
     * @return array<\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry>
     */
    private function filterLanguages(array $taxonomyEntries, string $query, ?string $languageCode = null): array
    {
        if (null === $languageCode) {
            return $taxonomyEntries;
        }

        $query = strtolower($query);

        $filteredEntries = [];
        foreach ($taxonomyEntries as $entry) {
            $names = $entry->getNames();

            if (!isset($names[$languageCode])) {
                continue;
            }

            if (str_contains(strtolower($names[$languageCode]), $query)) {
                $filteredEntries[] = $entry;
            }
        }

        return $filteredEntries;
    }
}
