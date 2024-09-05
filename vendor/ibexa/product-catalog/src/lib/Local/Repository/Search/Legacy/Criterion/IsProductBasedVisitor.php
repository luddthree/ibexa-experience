<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\IsProductBased;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageSchema;

final class IsProductBasedVisitor extends CriterionHandler
{
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof IsProductBased;
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
        $subSelect->select('product_storage.content_id')->from(StorageSchema::TABLE_NAME, 'product_storage');

        return $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL()
        );
    }
}
