<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductStock;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\ProductSpecificationIndexDataProvider;
use LogicException;

final class ProductStockVisitor extends CriterionVisitor
{
    private const ALLOWED_OPERATORS = [
        FieldValueCriterion::COMPARISON_EQ,
        FieldValueCriterion::COMPARISON_GT,
        FieldValueCriterion::COMPARISON_GTE,
        FieldValueCriterion::COMPARISON_LT,
        FieldValueCriterion::COMPARISON_LTE,
    ];

    public function canVisit(Criterion $criterion): bool
    {
        if (!$criterion instanceof ProductCriterionAdapter) {
            return false;
        }

        $productCriterion = $criterion->getProductCriterion();
        if (!$productCriterion instanceof ProductStock) {
            return false;
        }

        return in_array($productCriterion->getOperator(), self::ALLOWED_OPERATORS, true);
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductStock
     * > $criterion
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter $criterion
     */
    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null): string
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductStock $productCriterion */
        $productCriterion = $criterion->getProductCriterion();
        $operator = $productCriterion->getOperator();
        $value = $productCriterion->getValue();

        if ($value === null) {
            return ProductSpecificationIndexDataProvider::INDEX_PRODUCT_STOCK_IS_NULL . '_b:' . $this->toString(true);
        }

        if ($operator === FieldValueCriterion::COMPARISON_EQ) {
            return ProductSpecificationIndexDataProvider::INDEX_PRODUCT_STOCK . '_i:' . $this->toString($value);
        }

        if (in_array($operator, [FieldValueCriterion::COMPARISON_LT, FieldValueCriterion::COMPARISON_LTE], true)) {
            $range = $this->getRange($operator, null, $value);

            return ProductSpecificationIndexDataProvider::INDEX_PRODUCT_STOCK . '_i:' . $range;
        }

        if (in_array($operator, [FieldValueCriterion::COMPARISON_GT, FieldValueCriterion::COMPARISON_GTE], true)) {
            $range = $this->getRange($operator, $value, null);

            return ProductSpecificationIndexDataProvider::INDEX_PRODUCT_STOCK . '_i:' . $range;
        }

        throw new LogicException(sprintf(
            '%s is unable to handle %s criterion. Unknown operator "%s" encountered. Only "%s" are valid.',
            self::class,
            get_class($productCriterion),
            $operator,
            implode('", "', self::ALLOWED_OPERATORS),
        ));
    }
}
