<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Search\ElasticSearch\Criterion\Visitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as SearchCriterion;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\Taxonomy\Search\Query\Criterion;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsQuery;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;

final class TaxonomyEntryId implements CriterionVisitor
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    public function supports(SearchCriterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\TaxonomyEntryId;
    }

    /**
     * @param \Ibexa\Contracts\Taxonomy\Search\Query\Criterion\TaxonomyEntryId $term
     *
     * @return array<mixed>
     */
    public function visit(CriterionVisitor $dispatcher, SearchCriterion $term, LanguageFilter $languageFilter): array
    {
        $terms = $this->getTermQueries($term);
        if (count($terms) === 1) {
            $term = reset($terms);

            return $term->toArray();
        }

        $qb = new BoolQuery();
        foreach ($terms as $term) {
            $qb->addShould($term);
        }

        return $qb->toArray();
    }

    /**
     * @return array<\Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsQuery>
     */
    private function getTermQueries(Criterion\TaxonomyEntryId $criterion): array
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

        $terms = [];
        foreach ($valuesByTaxonomy as $taxonomy => $values) {
            $field = sprintf('taxonomy_entry_%s_mid', $taxonomy);
            $terms[] = new TermsQuery($field, $values);
        }

        return $terms;
    }
}
