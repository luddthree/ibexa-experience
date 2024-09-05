<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as ContentCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface as ProductCriterion;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterRegistry;

final class CriterionConverterVisitor extends CriterionVisitor
{
    private CriterionConverterRegistry $converterRegistry;

    public function __construct(CriterionConverterRegistry $converterRegistry)
    {
        $this->converterRegistry = $converterRegistry;
    }

    public function canVisit(Criterion $criterion): bool
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            return $this->converterRegistry->hasConverter($criterion->getProductCriterion());
        }

        return false;
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface
     * > $criterion
     */
    public function visit(Criterion $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        return $subVisitor->visit($this->convert($criterion->getProductCriterion()));
    }

    private function convert(ProductCriterion $criterion): ContentCriterion
    {
        return $this->converterRegistry->getConverter($criterion)->convert($criterion);
    }
}
