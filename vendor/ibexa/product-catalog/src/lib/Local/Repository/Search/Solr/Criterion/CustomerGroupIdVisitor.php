<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\CustomerGroupId;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;

final class CustomerGroupIdVisitor extends CriterionVisitor
{
    public function canVisit(Criterion $criterion): bool
    {
        return $criterion instanceof CustomerGroupId;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\CustomerGroupId $criterion
     */
    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null)
    {
        return sprintf(
            '(%s)',
            implode(
                ' OR ',
                array_map(
                    static fn (int $value): string => 'customer_group_id_id:"' . $value . '"',
                    (array)$criterion->value
                )
            )
        );
    }
}
