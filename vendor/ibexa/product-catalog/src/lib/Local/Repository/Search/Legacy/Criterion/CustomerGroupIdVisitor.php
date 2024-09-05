<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\CustomerGroupId;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Persistence\Gateway\StorageSchema;

final class CustomerGroupIdVisitor extends CriterionHandler
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof CustomerGroupId;
    }

    /**
     * @param array<string,mixed> $languageSettings
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select('customer_group.content_id')
            ->from(StorageSchema::TABLE_NAME, 'customer_group')
            ->where(
                $subSelect->expr()->in(
                    'customer_group.customer_group_id',
                    $queryBuilder->createNamedParameter(
                        (array)$criterion->value,
                        Connection::PARAM_INT_ARRAY
                    )
                )
            );

        return $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL()
        );
    }
}
