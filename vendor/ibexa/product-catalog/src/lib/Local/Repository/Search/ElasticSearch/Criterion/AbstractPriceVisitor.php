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
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractPriceCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\Operator;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RangeQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermQuery;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\PriceFieldNameBuilder;

abstract class AbstractPriceVisitor implements CriterionVisitor
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractPriceCriterion
     * > $criterion
     *
     * @return array<string,mixed>
     */
    final public function visit(
        CriterionVisitor $dispatcher,
        Criterion $criterion,
        LanguageFilter $languageFilter
    ): array {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractPriceCriterion */
        $productCriterion = $criterion->getProductCriterion();

        if ($productCriterion->getOperator() === Operator::EQ) {
            $query = new TermQuery();
            $query->withField($this->getTargetField($productCriterion));
            $query->withValue($productCriterion->getValue()->getAmount());

            return $query->toArray();
        }

        $query = new RangeQuery();
        $query->withField($this->getTargetField($productCriterion));
        $query->withOperator($productCriterion->getOperator());
        $query->withRange($productCriterion->getValue()->getAmount());

        return $query->toArray();
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
