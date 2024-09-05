<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\AttributeGroupIdentifierCriterion;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\NameCriterion;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractCriterionQuery;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalAnd;

/**
 * @extends \Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractCriterionQuery<
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractSortClause,
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface,
 * >
 */
final class AttributeDefinitionQuery extends AbstractCriterionQuery
{
    public function and(CriterionInterface ...$criteria): void
    {
        if (empty($criteria)) {
            return;
        }

        if ($this->getQuery() instanceof LogicalAnd) {
            $this->getQuery()->add(...$criteria);

            return;
        }

        $query = new LogicalAnd();

        if ($this->getQuery() !== null) {
            $query->add($this->getQuery());
        }

        $query->add(...$criteria);
        $this->setQuery($query);
    }

    /**
     * Always returns null, because it is not feasible to recreate Attribute Group from an identifier in a Query object.
     *
     * @deprecated no replacement is provided.
     *
     * @return \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface[]|null
     */
    public function getAttributesGroups(): ?array
    {
        trigger_deprecation(
            'ibexa/product-catalog',
            '4.0',
            sprintf(
                '%s is deprecated and should not be used. No replacement is provided.',
                __METHOD__,
            ),
        );

        return null;
    }

    /**
     * @deprecated use AttributeDefinitionQuery::and() method with AttributeGroupIdentifier instead.
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface[]|null $attributesGroups
     */
    public function setAttributesGroups(?array $attributesGroups): void
    {
        trigger_deprecation(
            'ibexa/product-catalog',
            '4.0',
            sprintf(
                '%s is deprecated and should not be used. Use %s::and() method with %s instead.',
                __METHOD__,
                self::class,
                AttributeGroupIdentifierCriterion::class,
            ),
        );

        if ($attributesGroups === null) {
            $this->removeCriterion('attribute_group.identifier');

            return;
        }

        $criterion = $this->findCriterion('attribute_group.identifier');

        $identifiers = array_map(static function (AttributeGroupInterface $attributeGroup): string {
            return $attributeGroup->getIdentifier();
        }, $attributesGroups);

        if ($criterion !== null) {
            $criterion->setValue($identifiers);
            $criterion->setOperator(FieldValueCriterion::COMPARISON_IN);
        } else {
            $this->and(new AttributeGroupIdentifierCriterion($identifiers));
        }
    }

    /**
     * @deprecated no replacement is provided.
     */
    public function getNamePrefix(): ?string
    {
        trigger_deprecation(
            'ibexa/product-catalog',
            '4.0',
            sprintf(
                '%s is deprecated and should not be used. No replacement is provided.',
                __METHOD__,
            ),
        );

        $criterion = $this->findCriterion('name_normalized');

        return $criterion === null ? null : $criterion->getValue();
    }

    /**
     * @deprecated use AttributeDefinitionQuery::and() method with NameCriterion instead.
     */
    public function setNamePrefix(?string $namePrefix): void
    {
        trigger_deprecation(
            'ibexa/product-catalog',
            '4.0',
            sprintf(
                '%s is deprecated and should not be used. Use %s::and() method with %s instead.',
                __METHOD__,
                self::class,
                NameCriterion::class,
            ),
        );

        if ($namePrefix === null) {
            $this->removeCriterion('name_normalized');

            return;
        }

        $criterion = $this->findCriterion('name_normalized');

        if ($criterion !== null) {
            $criterion->setValue($namePrefix);
            $criterion->setOperator(FieldValueCriterion::COMPARISON_STARTS_WITH);
        } else {
            $this->and(new NameCriterion($namePrefix));
        }
    }

    /**
     * Attempts to find and remove a criterion that references field provided as argument.
     */
    private function removeCriterion(string $field): void
    {
        $currentQuery = $this->getQuery();

        if ($currentQuery instanceof LogicalAnd) {
            $criteria = $currentQuery->getCriteria();

            foreach ($criteria as $criterion) {
                if ($criterion instanceof FieldValueCriterion && $criterion->getField() === $field) {
                    $currentQuery->remove($criterion);

                    return;
                }
            }

            return;
        }

        if ($currentQuery instanceof FieldValueCriterion && $currentQuery->getField() === $field) {
            $this->setQuery(null);
        }
    }

    /**
     * Attempts to find a Criterion instance that matches the field provided as argument.
     */
    private function findCriterion(string $field): ?FieldValueCriterion
    {
        $currentQuery = $this->getQuery();

        if ($currentQuery instanceof LogicalAnd) {
            $criteria = $currentQuery->getCriteria();

            foreach ($criteria as $criterion) {
                if ($criterion instanceof FieldValueCriterion && $criterion->getField() === $field) {
                    return $criterion;
                }
            }

            return null;
        }

        if ($currentQuery instanceof FieldValueCriterion && $currentQuery->getField() === $field) {
            return $currentQuery;
        }

        return null;
    }
}
