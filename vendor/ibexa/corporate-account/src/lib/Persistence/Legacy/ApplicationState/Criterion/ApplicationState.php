<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState\Criterion;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\CorporateAccount\Values\Query\Criterion\ApplicationState as ApplicationStateCriterion;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState\Gateway\StorageSchema;

final class ApplicationState extends CriterionHandler
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof ApplicationStateCriterion;
    }

    /**
     * @param array<string, mixed> $languageSettings
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ): string {
        $subSelect = $this->connection->createQueryBuilder();
        $value = (array)$criterion->value;
        $subSelect
            ->select(StorageSchema::COLUMN_APPLICATION_ID)
            ->from(StorageSchema::TABLE_NAME)
            ->andWhere(
                $queryBuilder->expr()->in(
                    StorageSchema::COLUMN_STATE,
                    $queryBuilder->createNamedParameter($value, Connection::PARAM_INT_ARRAY)
                )
            );

        return $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL()
        );
    }
}
