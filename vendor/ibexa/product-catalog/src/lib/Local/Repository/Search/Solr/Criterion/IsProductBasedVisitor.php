<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\IsProductBased;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;

final class IsProductBasedVisitor extends CriterionVisitor
{
    public function canVisit(Criterion $criterion): bool
    {
        return $criterion instanceof IsProductBased;
    }

    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null): string
    {
        return 'is_product_b:true';
    }
}
