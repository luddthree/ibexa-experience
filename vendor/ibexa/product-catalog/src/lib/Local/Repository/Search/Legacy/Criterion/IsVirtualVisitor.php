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
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IsVirtual;
use Ibexa\Core\Persistence\Legacy\Content\Type\Gateway;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\Gateway\StorageSchema;

/**
 * @internal
 */
final class IsVirtualVisitor extends CriterionHandler
{
    private const ALIAS_PRODUCT_TYPE_SETTINGS_TABLE = 'pt_set';
    private const ALIAS_FIELD_DEFINITION_TABLE = 'f_def';
    private const COLUMN_ID_FIELD_DEFINITION_TABLE = 'id';

    public function accept(Criterion $criterion): bool
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            return $criterion->getProductCriterion() instanceof IsVirtual;
        }

        return false;
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IsVirtual> $criterion
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter $criterion
     * @param array<string, mixed> $languageSettings
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        Criterion $criterion,
        array $languageSettings
    ) {
        $subQuery = $this->connection->createQueryBuilder();
        $subQuery
            ->select('contentclass_id')
            ->from(Gateway::FIELD_DEFINITION_TABLE, self::ALIAS_FIELD_DEFINITION_TABLE)
            ->innerJoin(
                self::ALIAS_FIELD_DEFINITION_TABLE,
                StorageSchema::TABLE_NAME,
                self::ALIAS_PRODUCT_TYPE_SETTINGS_TABLE,
                $subQuery->expr()->eq(
                    self::ALIAS_FIELD_DEFINITION_TABLE . '.' . self::COLUMN_ID_FIELD_DEFINITION_TABLE,
                    self::ALIAS_PRODUCT_TYPE_SETTINGS_TABLE . '.' . StorageSchema::COLUMN_FIELD_DEFINITION_ID
                )
            )
            ->where(
                $subQuery->expr()->eq(
                    self::ALIAS_PRODUCT_TYPE_SETTINGS_TABLE . '.' . StorageSchema::COLUMN_IS_VIRTUAL,
                    $queryBuilder->createNamedParameter(
                        $criterion->getProductCriterion()->isVirtual(),
                        ParameterType::BOOLEAN
                    )
                )
            );

        return $queryBuilder->expr()->in(
            'c.contentclass_id',
            $subQuery->getSQL()
        );
    }
}
