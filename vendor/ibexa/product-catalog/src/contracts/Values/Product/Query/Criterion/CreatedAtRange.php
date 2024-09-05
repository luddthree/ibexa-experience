<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use DateTimeInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use InvalidArgumentException;

final class CreatedAtRange extends CompositeCriterion
{
    private ?DateTimeInterface $min = null;

    private ?DateTimeInterface $max = null;

    public function __construct(?DateTimeInterface $min = null, ?DateTimeInterface $max = null)
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

    public function getMin(): ?DateTimeInterface
    {
        return $this->min;
    }

    public function getMax(): ?DateTimeInterface
    {
        return $this->max;
    }

    private function getCriterion(): CriterionInterface
    {
        $minCriterion = $this->min ? new CreatedAt($this->min, Operator::GTE) : null;
        $maxCriterion = $this->max ? new CreatedAt($this->max, Operator::LTE) : null;

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
