<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RangeQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsQuery;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\AttributeFieldNameBuilder;

/**
 * @template TCriterion of \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute
 */
abstract class AbstractAttributeVisitor implements CriterionVisitor
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

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter<TCriterion> $criterion
     *
     * @return array<mixed>
     */
    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $productCriterion = $criterion->getProductCriterion();
        $operator = $productCriterion->getOperator();

        $value = $this->getCriterionValue($productCriterion);
        if ($value === null) {
            $query = new TermQuery();
            $query->withField($this->getNullField($productCriterion));
            $query->withValue(true);
        } elseif (
            $operator === FieldValueCriterion::COMPARISON_EQ
        ) {
            $query = new TermQuery();
            $query->withField($this->getTargetField($productCriterion));
            $query->withValue($value);
        } elseif (
            $operator === FieldValueCriterion::COMPARISON_IN
        ) {
            $query = new TermsQuery();
            $query->withField($this->getTargetField($productCriterion));
            $query->withValue($value);
        } else {
            $query = new RangeQuery();
            $query->withField($this->getTargetField($productCriterion));
            $query->withOperator($productCriterion->getOperator());
            $range = (array)$value;
            $query->withRange(...$range);
        }

        return $query->toArray();
    }

    final public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        if (!$criterion instanceof ProductCriterionAdapter) {
            return false;
        }

        $criterionClass = $this->getCriterionClass();

        return $criterion->getProductCriterion() instanceof $criterionClass;
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
