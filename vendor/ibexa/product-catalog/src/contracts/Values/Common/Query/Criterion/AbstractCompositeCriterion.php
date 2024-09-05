<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion;

abstract class AbstractCompositeCriterion implements CriterionInterface
{
    /** @var array<\Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface> */
    private array $criteria;

    public function __construct(CriterionInterface ...$criteria)
    {
        $this->criteria = $criteria;
    }

    public function add(CriterionInterface ...$criteria): void
    {
        $this->setCriteria(
            ...$this->criteria,
            ...$criteria,
        );
    }

    public function remove(CriterionInterface ...$criteria): void
    {
        $this->setCriteria(...array_filter(
            $this->criteria,
            static function (CriterionInterface $criterion) use ($criteria): bool {
                return !in_array($criterion, $criteria, true);
            },
        ));
    }

    public function setCriteria(CriterionInterface ...$criteria): void
    {
        $this->criteria = $criteria;
    }

    /**
     * @return array<\Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface>
     */
    final public function getCriteria(): array
    {
        return $this->criteria;
    }
}
