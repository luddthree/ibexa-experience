<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IsVirtual;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\ProductSpecificationIndexDataProvider;

/**
 * @internal
 */
final class IsVirtualVisitor extends CriterionVisitor
{
    public function canVisit(Criterion $criterion): bool
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            return $criterion->getProductCriterion() instanceof IsVirtual;
        }

        return false;
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IsVirtual
     * > $criterion
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter $criterion
     */
    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null): string
    {
        $isVirtual = $criterion->getProductCriterion()->isVirtual();

        return ProductSpecificationIndexDataProvider::INDEX_IS_VIRTUAL . '_b:' . $this->toString($isVirtual);
    }
}
