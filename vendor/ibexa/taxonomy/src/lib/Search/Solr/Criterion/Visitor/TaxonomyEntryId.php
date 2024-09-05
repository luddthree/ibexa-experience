<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Search\Solr\Criterion\Visitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as SearchCriterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\Contracts\Taxonomy\Search\Query\Criterion;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;

final class TaxonomyEntryId extends CriterionVisitor
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    public function canVisit(SearchCriterion $criterion): bool
    {
        return
            $criterion instanceof Criterion\TaxonomyEntryId
            && (($criterion->operator ?: Operator::IN) === Operator::IN
                || $criterion->operator === Operator::EQ);
    }

    /**
     * @param \Ibexa\Contracts\Taxonomy\Search\Query\Criterion\TaxonomyEntryId $criterion
     *
     * @phpstan-return non-empty-string
     */
    public function visit(SearchCriterion $criterion, CriterionVisitor $subVisitor = null): string
    {
        $valuesByTaxonomy = $this->getEntriesGroupedByTaxonomy($criterion);

        return $this->buildCondition($valuesByTaxonomy);
    }

    /**
     * @return array<string, int[]>
     */
    private function getEntriesGroupedByTaxonomy(Criterion\TaxonomyEntryId $criterion): array
    {
        /** @var int[] $value */
        $value = is_array($criterion->value) ? $criterion->value : [$criterion->value];

        $valuesByTaxonomy = [];
        if ($criterion->target !== null) {
            $valuesByTaxonomy[$criterion->target] = $value;
        } else {
            trigger_deprecation(
                'ibexa/taxonomy',
                '4.5',
                'Not setting %s target (taxonomy identifier) has been deprecated and will be removed in 5.0.',
                Criterion\TaxonomyEntryId::class,
            );
            foreach ($value as $id) {
                try {
                    $entry = $this->taxonomyService->loadEntryById($id);
                    $taxonomy = $entry->getTaxonomy();
                } catch (TaxonomyEntryNotFoundException $e) {
                    $taxonomy = '__unknown__';
                }
                $valuesByTaxonomy[$taxonomy][] = $id;
            }
        }

        return $valuesByTaxonomy;
    }

    /**
     * @param array<string, int[]> $valuesByTaxonomy
     *
     * @phpstan-return non-empty-string
     */
    private function buildCondition(array $valuesByTaxonomy): string
    {
        $conditions = [];
        foreach ($valuesByTaxonomy as $taxonomy => $entryIds) {
            $conditions[] = implode(
                ' OR ',
                array_map(
                    static fn (int $entryId): string => sprintf('taxonomy_entry_%s_mid:"%s"', $taxonomy, $entryId),
                    $entryIds
                )
            );
        }

        $conditions = implode(' OR ', $conditions);

        return "($conditions)";
    }
}
