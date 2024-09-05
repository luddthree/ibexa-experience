<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

/**
 * @template TValue
 */
abstract class AbstractAttribute implements CriterionInterface
{
    private string $identifier;

    /**
     * @phpstan-var \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion::COMPARISON_*
     */
    private string $operator = FieldValueCriterion::COMPARISON_EQ;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return TValue|null
     */
    abstract public function getValue();

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion::COMPARISON_* $operator
     */
    public function setOperator(string $operator): void
    {
        $this->operator = $operator;
    }

    /**
     * @phpstan-return \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion::COMPARISON_*
     */
    public function getOperator(): string
    {
        return $this->operator;
    }
}
