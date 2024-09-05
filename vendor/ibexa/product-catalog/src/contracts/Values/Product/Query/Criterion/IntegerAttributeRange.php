<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use InvalidArgumentException;

final class IntegerAttributeRange extends CompositeCriterion
{
    private ?int $min = null;

    private ?int $max = null;

    private string $identifier;

    public function __construct(string $identifier, ?int $min = null, ?int $max = null)
    {
        $this->identifier = $identifier;

        if ($min === null && $max === null) {
            throw new InvalidArgumentException(
                'One of the minimum or maximum values must be provided.'
            );
        }

        $this->min = $min;
        $this->max = $max;

        parent::__construct($this->getCriterion());
    }

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    private function getCriterion(): CriterionInterface
    {
        $minCriterion = null;
        $maxCriterion = null;

        if ($this->min !== null) {
            $minCriterion = new IntegerAttribute($this->identifier, $this->min);
            $minCriterion->setOperator(FieldValueCriterion::COMPARISON_GTE);
        }

        if ($this->max !== null) {
            $maxCriterion = new IntegerAttribute($this->identifier, $this->max);
            $maxCriterion->setOperator(FieldValueCriterion::COMPARISON_LTE);
        }

        if ($minCriterion && $maxCriterion) {
            return new LogicalAnd([
                $minCriterion,
                $maxCriterion,
            ]);
        }

        $criterion = $minCriterion ?? $maxCriterion;

        if ($criterion === null) {
            throw new InvalidArgumentException('Invalid range: missing at least one bound');
        }

        return $criterion;
    }
}
