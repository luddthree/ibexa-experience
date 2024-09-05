<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use InvalidArgumentException;
use Money\Money;

final class BasePriceRange extends CompositeCriterion
{
    private ?Money $min = null;

    private ?Money $max = null;

    public function __construct(?Money $min = null, ?Money $max = null)
    {
        if ($min === null && $max === null) {
            throw new InvalidArgumentException(
                'One of the minimum or maximum values must be provided.'
            );
        }

        $this->min = $min;
        $this->max = $max;

        parent::__construct($this->getCriterion());
    }

    public function getMin(): ?Money
    {
        return $this->min;
    }

    public function getMax(): ?Money
    {
        return $this->max;
    }

    private function getCriterion(): CriterionInterface
    {
        $minCriterion = $this->min ? new BasePrice($this->min, Operator::GTE) : null;
        $maxCriterion = $this->max ? new BasePrice($this->max, Operator::LTE) : null;

        if ($minCriterion && $maxCriterion) {
            if ($minCriterion->getCurrency()->getCode() !== $maxCriterion->getCurrency()->getCode()) {
                throw new InvalidArgumentException('The currency must be the same in both ranges.');
            }

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
