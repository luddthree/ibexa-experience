<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCode;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;

final class ProductCodeVisitor extends CriterionVisitor
{
    public function canVisit(Criterion $criterion): bool
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            return $criterion->getProductCriterion() instanceof ProductCode;
        }

        return false;
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCode> $criterion
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter $criterion
     */
    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null): string
    {
        $codes = $criterion->getProductCriterion()->getCodes();

        if (count($codes) === 0) {
            return '(NOT *:*)';
        }

        return '(' .
            implode(
                ' OR ',
                array_map(
                    function (string $value): string {
                        return 'product_code_id:"' . $this->escapeQuote($value, true) . '"';
                    },
                    $codes
                )
            ) .
        ')';
    }
}
