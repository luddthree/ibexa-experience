<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageSchema;

final class ProductAvailabilityVisitor extends CriterionHandler
{
    public function accept(Criterion $criterion): bool
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            return $criterion->getProductCriterion() instanceof ProductAvailability;
        }

        return false;
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability> $criterion
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter $criterion
     * @param array<string,mixed> $languageSettings
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $isAvailable = $criterion->getProductCriterion()->isAvailable();

        $expr = [
            $queryBuilder->expr()->eq(
                'availability.availability',
                $queryBuilder->createNamedParameter($isAvailable, ParameterType::BOOLEAN)
            ),
        ];
        if (!$isAvailable) {
            $expr[] = $queryBuilder->expr()->isNull(
                'availability.availability',
            );
        }
        $whereClause = $queryBuilder->expr()->or(
            ...$expr
        );
        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select('product_storage.content_id')
            ->from(StorageSchema::TABLE_NAME, 'product_storage')
            ->where(
                $whereClause
            )->leftJoin(
                'product_storage',
                'ibexa_product_specification_availability',
                'availability',
                $queryBuilder->expr()->eq(
                    'product_storage.code',
                    'availability.product_code',
                ),
            );

        return $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL()
        );
    }
}
