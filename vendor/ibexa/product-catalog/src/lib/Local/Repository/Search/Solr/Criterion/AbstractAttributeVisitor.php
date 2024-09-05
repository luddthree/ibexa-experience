<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\AttributeFieldNameBuilder;

/**
 * @template TCriterion of \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute
 */
abstract class AbstractAttributeVisitor extends CriterionVisitor
{
    /**
     * @return class-string<TCriterion>
     */
    abstract protected function getCriterionClass(): string;

    /**
     * @param TCriterion $criterion
     *
     * @phpstan-return "i"|"b"|"f"|"s"
     */
    abstract protected function getAttributeType(AbstractAttribute $criterion): string;

    public function canVisit(Criterion $criterion): bool
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            return is_a($criterion->getProductCriterion(), $this->getCriterionClass());
        }

        return false;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<TCriterion> $criterion
     */
    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null)
    {
        $productCriterion = $criterion->getProductCriterion();
        $value = $this->getCriterionValue($productCriterion);
        if ($value === null) {
            return sprintf(
                '%s:true',
                $this->getNullField($productCriterion),
            );
        }

        $operator = $productCriterion->getOperator();
        if (in_array($operator, [
            Criterion\Operator::GT,
            Criterion\Operator::GTE,
        ], true)) {
            return sprintf(
                '%s:%s',
                $this->getTargetField($productCriterion),
                $this->getRange($operator, $value, null),
            );
        }

        if (in_array($operator, [
            Criterion\Operator::LT,
            Criterion\Operator::LTE,
        ], true)) {
            return sprintf(
                '%s:%s',
                $this->getTargetField($productCriterion),
                $this->getRange($operator, null, $value),
            );
        }

        return sprintf(
            '(%s)',
            implode(
                ' OR ',
                array_map(
                    fn ($value): string => sprintf(
                        '%s:%s',
                        $this->getTargetField($productCriterion),
                        $this->escapeExpressions($this->toString($value)),
                    ),
                    is_array($value) ? $value : [$value]
                )
            )
        );
    }

    /**
     * @param TCriterion $criterion
     *
     * @return mixed|null
     */
    protected function getCriterionValue(AbstractAttribute $criterion)
    {
        return $criterion->getValue();
    }

    /**
     * @param TCriterion $criterion
     */
    final protected function getTargetField(AbstractAttribute $criterion): string
    {
        $fieldNameBuilder = $this->getAttributeFieldNameBuilder($criterion);

        return sprintf(
            '%s_%s',
            $fieldNameBuilder->build(),
            $this->getAttributeType($criterion),
        );
    }

    /**
     * @param TCriterion $criterion
     */
    final protected function getNullField(AbstractAttribute $criterion): string
    {
        $fieldNameBuilder = $this->getAttributeFieldNameBuilder($criterion);

        return $fieldNameBuilder->build() . '_b';
    }

    /**
     * @param TCriterion $criterion
     */
    protected function getAttributeFieldNameBuilder(AbstractAttribute $criterion): AttributeFieldNameBuilder
    {
        $fieldNameBuilder = new AttributeFieldNameBuilder($criterion->getIdentifier());

        if ($this->getCriterionValue($criterion) === null) {
            $fieldNameBuilder->withIsNull();
        } else {
            $fieldNameBuilder->withField('value');
        }

        return $fieldNameBuilder;
    }
}
