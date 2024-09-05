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
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractPriceCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CustomPrice;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\PriceFieldNameBuilder;

final class CustomPriceVisitor extends AbstractPriceVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            return $criterion->getProductCriterion() instanceof CustomPrice;
        }

        return false;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CustomPrice $criterion
     */
    protected function buildPriceFieldName(
        PriceFieldNameBuilder $builder,
        AbstractPriceCriterion $criterion
    ): void {
        if ($criterion->getCustomerGroup() !== null) {
            $builder->withCustomerGroup($criterion->getCustomerGroup()->getIdentifier());
        }
    }
}
