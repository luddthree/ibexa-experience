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
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductStock;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RangeQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermQuery;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\ProductSpecificationIndexDataProvider;

final class ProductStockVisitor implements CriterionVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        if (!$criterion instanceof ProductCriterionAdapter) {
            return false;
        }

        $productCriterion = $criterion->getProductCriterion();
        if (!$productCriterion instanceof ProductStock) {
            return false;
        }

        return true;
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductStock,
     * > $criterion
     *
     * @return array<mixed>
     */
    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductStock $productCriterion */
        $productCriterion = $criterion->getProductCriterion();
        $operator = $productCriterion->getOperator();

        $value = $productCriterion->getValue();

        if ($value === null) {
            $query = new TermQuery();
            $query->withField(ProductSpecificationIndexDataProvider::INDEX_PRODUCT_STOCK_IS_NULL . '_b');
            $query->withValue(true);
        } elseif ($operator === FieldValueCriterion::COMPARISON_EQ) {
            $query = new TermQuery();
            $query->withField(ProductSpecificationIndexDataProvider::INDEX_PRODUCT_STOCK . '_i');
            $query->withValue($value);
        } else {
            $query = new RangeQuery();
            $query->withField(ProductSpecificationIndexDataProvider::INDEX_PRODUCT_STOCK . '_i');
            $query->withOperator($productCriterion->getOperator());
            $query->withRange($value);
        }

        return $query->toArray();
    }
}
