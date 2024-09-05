<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Search\Solr\Criterion\Visitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as SearchCriterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\Contracts\Taxonomy\Search\Query\Criterion;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use LogicException;
use Traversable;

final class TaxonomySubtree extends CriterionVisitor
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(
        TaxonomyServiceInterface $taxonomyService
    ) {
        $this->taxonomyService = $taxonomyService;
    }

    public function supports(SearchCriterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\TaxonomySubtree;
    }

    public function canVisit(SearchCriterion $criterion)
    {
        return
            $criterion instanceof Criterion\TaxonomySubtree
            && $criterion->operator === Operator::EQ;
    }

    public function visit(SearchCriterion $criterion, CriterionVisitor $subVisitor = null): string
    {
        if (!is_array($criterion->value) || empty($criterion->value)) {
            throw new LogicException('Int in array expected.');
        }

        $categoryEntryId = (int)reset($criterion->value);
        $categoryPathString = $this->getPathString($categoryEntryId);

        $wildcard = $categoryPathString . '/*';

        return "taxonomy_entry_path_mid:$wildcard";
    }

    private function getPathString(int $entryId): string
    {
        $entry = $this->taxonomyService->loadEntryById($entryId);

        $path = $this->taxonomyService->getPath($entry);
        if ($path instanceof Traversable) {
            $path = iterator_to_array($path);
        }
        $pathIds = array_map(static fn (TaxonomyEntry $entry): int => $entry->id, $path);

        return implode('/', $pathIds);
    }
}
