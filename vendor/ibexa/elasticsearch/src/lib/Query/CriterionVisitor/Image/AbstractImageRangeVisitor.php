<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor\Image;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RangeQuery;

abstract class AbstractImageRangeVisitor extends AbstractImageVisitor
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function visit(
        CriterionVisitor $dispatcher,
        Criterion $criterion,
        LanguageFilter $languageFilter
    ): array {
        $query = new BoolQuery();
        foreach ($this->getSearchFieldNames($criterion) as $fieldName) {
            $rangeQuery = new RangeQuery(
                $fieldName,
                $criterion->operator,
                $criterion->value
            );
            $query->addMust($rangeQuery);
        }

        return $query->toArray();
    }
}
