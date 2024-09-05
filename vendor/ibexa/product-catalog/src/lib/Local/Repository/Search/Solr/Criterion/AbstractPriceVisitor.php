<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractPriceCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\Operator;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\PriceFieldNameBuilder;

abstract class AbstractPriceVisitor extends CriterionVisitor
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractPriceCriterion
     * > $criterion
     */
    final public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null): string
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractPriceCriterion $productCriterion */
        $productCriterion = $criterion->getProductCriterion();

        $targetField = $this->getTargetField($productCriterion);
        $operator = $productCriterion->getOperator();
        $amount = $productCriterion->getValue()->getAmount();

        if ($operator === Operator::EQ) {
            return $targetField . ':' . $amount;
        }

        if (in_array($operator, [Operator::GT, Operator::GTE], true)) {
            return $targetField . ':' . $this->getRange($operator, $amount, null);
        }

        return $targetField . ':' . $this->getRange($operator, null, $amount);
    }

    protected function buildPriceFieldName(
        PriceFieldNameBuilder $builder,
        AbstractPriceCriterion $criterion
    ): void {
    }

    private function getTargetField(AbstractPriceCriterion $criterion): string
    {
        $builder = new PriceFieldNameBuilder($criterion->getCurrency()->getCode());
        $this->buildPriceFieldName($builder, $criterion);

        return $builder->build() . '_i';
    }
}
