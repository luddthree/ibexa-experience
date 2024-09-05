<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;

/**
 * @template T of \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute
 *
 * @extends \Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion\AbstractAttributeVisitor<T>
 */
abstract class SingleFieldAttributeVisitor extends AbstractAttributeVisitor
{
    abstract protected function getAttributeValueStorageTable(): string;

    /**
     * @phpstan-param T $criterion
     */
    abstract protected function getAttributeValueStorageColumn(AbstractAttribute $criterion): string;

    final protected function joinSubTable(QueryBuilder $qb): void
    {
        $qb->join(
            'attribute_storage',
            $this->getAttributeValueStorageTable(),
            'attribute_value_storage',
            $qb->expr()->eq(
                'attribute_value_storage.id',
                'attribute_storage.id',
            )
        );
    }

    final protected function createComparison(QueryBuilder $qb, AbstractAttribute $criterion)
    {
        $fieldName = 'attribute_value_storage.' . $this->getAttributeValueStorageColumn($criterion);
        $value = $this->getCriterionValue($criterion);
        if ($value === null) {
            return $qb->expr()->isNull($fieldName);
        }

        $type = $this->getCriterionBindType($criterion);

        if (FieldValueCriterion::COMPARISON_IN === $criterion->getOperator()) {
            $type += Connection::ARRAY_PARAM_OFFSET;

            return $qb->expr()->in(
                $fieldName,
                $qb->createNamedParameter(
                    $value,
                    $type
                ),
            );
        }

        return $qb->expr()->comparison(
            $fieldName,
            $criterion->getOperator(),
            $qb->createNamedParameter(
                $value,
                $type
            ),
        );
    }

    /**
     * @phpstan-param T $criterion
     *
     * @return mixed|null
     */
    protected function getCriterionValue(AbstractAttribute $criterion)
    {
        return $criterion->getValue();
    }

    /**
     * @phpstan-param T $criterion
     */
    protected function getCriterionBindType(AbstractAttribute $criterion): int
    {
        return Type::getType(Types::STRING)->getBindingType();
    }
}
