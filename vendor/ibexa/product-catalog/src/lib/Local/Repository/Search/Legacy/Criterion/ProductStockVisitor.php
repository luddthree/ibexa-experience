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
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductStock;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageSchema;

final class ProductStockVisitor extends CriterionHandler
{
    public function accept(Criterion $criterion): bool
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            return $criterion->getProductCriterion() instanceof ProductStock;
        }

        return false;
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductStock> $criterion
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
        /** @var \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductStock $productCriterion */
        $productCriterion = $criterion->getProductCriterion();
        $stockValue = $productCriterion->getValue();

        if ($stockValue === null) {
            $whereClause = $queryBuilder->expr()->isNull('availability.stock');
        } else {
            $whereClause = $queryBuilder->expr()->comparison(
                'availability.stock',
                $productCriterion->getOperator(),
                $queryBuilder->createNamedParameter($stockValue, ParameterType::INTEGER)
            );
        }

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
