<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability;
use Ibexa\Elasticsearch\Query\CriterionVisitor\AbstractTermVisitor;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\ProductSpecificationIndexDataProvider;

final class ProductAvailabilityVisitor extends AbstractTermVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            return $criterion->getProductCriterion() instanceof ProductAvailability;
        }

        return false;
    }

    protected function getTargetField(Criterion $criterion): string
    {
        return ProductSpecificationIndexDataProvider::INDEX_PRODUCT_AVAILABILITY . '_b';
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability
     * > $criterion
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter $criterion
     */
    protected function getTargetValue($criterion): bool
    {
        return $criterion->getProductCriterion()->isAvailable();
    }
}
