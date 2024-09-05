<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\FullText;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Core\Search\Common\FieldNameResolver;

final class FullTextVisitor implements CriterionVisitor
{
    private const MAX_EDIT_DISTANCE = 2;

    /** @var \Ibexa\Core\Search\Common\FieldNameResolver */
    private $fieldNameResolver;

    public function __construct(FieldNameResolver $fieldNameResolver)
    {
        $this->fieldNameResolver = $fieldNameResolver;
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof FullText;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $body = [
            'query' => $criterion->value,
            'fields' => [],
        ];

        if (is_float($criterion->fuzziness)) {
            $body['fuzziness'] = $this->convertToEditDistance(
                $criterion->fuzziness,
                $criterion->value
            );
        } elseif (is_int($criterion->fuzziness)) {
            $body['fuzziness'] = $criterion->fuzziness;
        }

        foreach ($criterion->boost as $field => $boost) {
            $searchFields = $this->fieldNameResolver->getFieldTypes($criterion, $field);
            foreach ($searchFields as $name => $fieldType) {
                $body['fields'][] = "{$name}^{$boost}";
            }
        }

        $body['fields'][] = '*_fulltext';

        return [
            'multi_match' => $body,
        ];
    }

    /**
     * Convert floating point fuzziness to edit distance using the formula: length(term) * (1.0 -
     * fuzziness).
     *
     * Base on https://github.com/apache/lucene-solr/blob/branch_4x/lucene/core/src/java/org/apache/lucene/search/FuzzyQuery.java#L232-L242
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/7.7/common-options.html#fuzziness
     */
    private function convertToEditDistance(float $fuzziness, string $term): int
    {
        if ($fuzziness >= 1.0) {
            return 0;
        }

        if ($fuzziness <= 0.0) {
            return self::MAX_EDIT_DISTANCE;
        }

        return (int)min((1 - $fuzziness) * mb_strlen($term), self::MAX_EDIT_DISTANCE);
    }
}

class_alias(FullTextVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\FullTextVisitor');
