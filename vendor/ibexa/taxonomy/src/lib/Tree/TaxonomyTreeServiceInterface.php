<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Tree;

/**
 * @internal
 */
interface TaxonomyTreeServiceInterface
{
    public const DEFAULT_LIMIT = 100;

    /**
     * @return array<int, mixed>
     */
    public function loadTreeRoot(string $taxonomyName): array;

    /**
     * @return array<string, mixed>
     */
    public function loadNode(int $nodeId): array;

    /**
     * @param array<int> $nodeIds
     *
     * @return array<int, mixed>
     */
    public function loadSubtree(array $nodeIds): array;

    /**
     * @return array<int, mixed>
     */
    public function findNodes(
        string $query,
        ?string $languageCode = null,
        ?int $limit = self::DEFAULT_LIMIT,
        int $offset = 0,
        string $taxonomy = null
    ): array;

    public function countIndirectChildren(int $nodeId): int;
}
