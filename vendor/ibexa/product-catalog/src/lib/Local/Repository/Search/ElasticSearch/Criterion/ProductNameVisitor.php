<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductName;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\WildcardQuery;

final class ProductNameVisitor implements CriterionVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            return $criterion->getProductCriterion() instanceof ProductName;
        }

        return false;
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductName
     * > $criterion
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter $criterion
     *
     * @return array<string,mixed>
     */
    public function visit(
        CriterionVisitor $dispatcher,
        Criterion $criterion,
        LanguageFilter $languageFilter
    ): array {
        $terms = new WildcardQuery();
        $terms->withField('content_translated_name_s');
        $terms->withValue($criterion->getProductCriterion()->getName());

        return $terms->toArray();
    }
}
