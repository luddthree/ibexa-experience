<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\GraphQL\Resolver;

use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use InvalidArgumentException;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;

/**
 * @internal
 */
final class TaxonomyEntryResolver implements QueryInterface
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(
        TaxonomyServiceInterface $taxonomyService
    ) {
        $this->taxonomyService = $taxonomyService;
    }

    public function resolveChildrenByParentId(Argument $argument, int $id): Connection
    {
        $parentEntry = $this->taxonomyService->loadEntryById($id);

        $paginator = new Paginator(function (int $offset, ?int $limit) use ($parentEntry): array {
            return iterator_to_array(
                $this->taxonomyService->loadEntryChildren($parentEntry, $limit ?? 30, $offset)
            );
        });

        return $paginator->auto(
            $argument,
            function () use ($parentEntry): int {
                return $this->taxonomyService->countEntryChildren($parentEntry);
            }
        );
    }

    public function resolveTaxonomyEntry(Argument $argument, ?string $taxonomyTypeName = null): TaxonomyEntry
    {
        if (isset($argument['id'])) {
            return $this->taxonomyService->loadEntryById($argument['id']);
        }

        if (isset($argument['identifier'])) {
            return $this->taxonomyService->loadEntryByIdentifier($argument['identifier'], $taxonomyTypeName);
        }

        if (isset($argument['contentId'])) {
            return $this->taxonomyService->loadEntryByContentId($argument['contentId']);
        }

        throw new InvalidArgumentException('Cannot resolve TaxonomyEntry from argument set');
    }

    public function resolveTaxonomyEntries(Argument $argument, string $taxonomyName): Connection
    {
        $paginator = new Paginator(function (int $offset, ?int $limit) use ($taxonomyName): array {
            return iterator_to_array(
                $this->taxonomyService->loadAllEntries($taxonomyName, $limit ?? 30, $offset)
            );
        });

        return $paginator->auto(
            $argument,
            function () use ($taxonomyName): int {
                return $this->taxonomyService->countAllEntries($taxonomyName);
            }
        );
    }
}
