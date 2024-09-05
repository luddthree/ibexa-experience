<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as ContentCriterion;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface as ProductCriterion;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterRegistry;

final class CriterionConverterVisitor implements CriterionVisitor
{
    private CriterionConverterRegistry $converterRegistry;

    public function __construct(CriterionConverterRegistry $converterRegistry)
    {
        $this->converterRegistry = $converterRegistry;
    }

    public function supports(ContentCriterion $criterion, LanguageFilter $languageFilter): bool
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            return $this->converterRegistry->hasConverter($criterion->getProductCriterion());
        }

        return false;
    }

    /**
     * @phpstan-param ProductCriterionAdapter<ProductCriterion> $criterion
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter $criterion
     *
     * @return array<string,mixed>
     */
    public function visit(
        CriterionVisitor $dispatcher,
        ContentCriterion $criterion,
        LanguageFilter $languageFilter
    ): array {
        return $dispatcher->visit(
            $dispatcher,
            $this->convert($criterion->getProductCriterion()),
            $languageFilter
        );
    }

    private function convert(ProductCriterion $criterion): ContentCriterion
    {
        return $this->converterRegistry->getConverter($criterion)->convert($criterion);
    }
}
