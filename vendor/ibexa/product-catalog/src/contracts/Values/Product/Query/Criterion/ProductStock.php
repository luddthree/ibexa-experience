<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class ProductStock implements CriterionInterface
{
    /**
     * @phpstan-var \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion::COMPARISON_*
     */
    private string $operator;

    private ?int $value;

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion::COMPARISON_* $operator
     */
    public function __construct(?int $value, string $operator = FieldValueCriterion::COMPARISON_EQ)
    {
        $this->value = $value;
        $this->operator = $operator;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

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
