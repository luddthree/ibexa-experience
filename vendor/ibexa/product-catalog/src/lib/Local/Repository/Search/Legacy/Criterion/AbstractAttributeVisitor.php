<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\Core\Persistence\Legacy\Content\Gateway as ContentGateway;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageSchema as ProductStorageSchema;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Gateway\StorageSchema as AttributeStorageSchema;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\Gateway\Schema as AttributeDefinitionStorageSchema;

/**
 * @template T of \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute
 */
abstract class AbstractAttributeVisitor extends CriterionHandler
{
    /**
     * @return class-string<T>
     */
    abstract protected function getSupportedProductCriterion(): string;

    public function accept(Criterion $criterion): bool
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            $supportedProductCriterion = $this->getSupportedProductCriterion();

            return $criterion->getProductCriterion() instanceof $supportedProductCriterion;
        }

        return false;
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<T> $criterion
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
        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select('product_storage.content_id')
            ->distinct()
            ->from(ProductStorageSchema::TABLE_NAME, 'product_storage')
            ->join(
                'product_storage',
                AttributeStorageSchema::TABLE_NAME,
                'attribute_storage',
                $queryBuilder->expr()->eq(
                    'product_storage.' . ProductStorageSchema::COLUMN_ID,
                    'attribute_storage.' . AttributeStorageSchema::COLUMN_PRODUCT_SPECIFICATION_ID,
                ),
            )
            // Ensure only published content
            ->join(
                'product_storage',
                ContentGateway::CONTENT_ITEM_TABLE,
                'content_storage',
                (string)$queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq(
                        'product_storage.' . ProductStorageSchema::COLUMN_CONTENT_ID,
                        'content_storage.id',
                    ),
                    $queryBuilder->expr()->eq(
                        'product_storage.' . ProductStorageSchema::COLUMN_VERSION_NO,
                        'content_storage.current_version',
                    ),
                ),
            )
            ->join(
                'attribute_storage',
                AttributeDefinitionStorageSchema::TABLE_NAME,
                'attribute_definition_storage',
                $queryBuilder->expr()->eq(
                    'attribute_storage.' . AttributeStorageSchema::COLUMN_ATTRIBUTE_DEFINITION_ID,
                    'attribute_definition_storage.' . AttributeDefinitionStorageSchema::COLUMN_ID,
                ),
            )
        ;

        $this->joinSubTable($subSelect);

        $productCriterion = $criterion->getProductCriterion();
        $identifier = $productCriterion->getIdentifier();

        $comparison = $this->createComparison($queryBuilder, $productCriterion);

        $whereClause = $queryBuilder->expr()->and(
            $queryBuilder->expr()->eq(
                'attribute_definition_storage.' . AttributeDefinitionStorageSchema::COLUMN_IDENTIFIER,
                $queryBuilder->createNamedParameter($identifier),
            ),
            $comparison,
        );

        $subSelect->andWhere($whereClause);

        return $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL()
        );
    }

    abstract protected function joinSubTable(QueryBuilder $qb): void;

    /**
     * @param T $criterion
     *
     * @return \Doctrine\DBAL\Query\Expression\CompositeExpression|string
     */
    abstract protected function createComparison(QueryBuilder $qb, AbstractAttribute $criterion);
}
