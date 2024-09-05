<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Filter;

use Doctrine\DBAL\Connection;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\CustomerGroupId;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Persistence\Gateway\StorageSchema;

final class CustomerGroupIdQueryBuilder implements CriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof CustomerGroupId;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\CustomerGroupId $criterion
     */
    public function buildQueryConstraint(FilteringQueryBuilder $queryBuilder, FilteringCriterion $criterion): ?string
    {
        $queryBuilder->joinOnce(
            'content',
            StorageSchema::TABLE_NAME,
            'customer_group',
            'content.id = customer_group.content_id'
        );

        return $queryBuilder->expr()->in(
            'customer_group.customer_group_id',
            $queryBuilder->createNamedParameter(
                (array)$criterion->value,
                Connection::PARAM_INT_ARRAY
            )
        );
    }
}
