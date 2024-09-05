<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\CriterionMapper;

use Doctrine\Common\Collections\Expr\CompositeExpression;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\CriterionMapperInterface;
use Ibexa\ProductCatalog\Local\Repository\CriterionMapper;

/**
 * @implements \Ibexa\Contracts\ProductCatalog\Values\Common\Query\CriterionMapperInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\AbstractCompositeCriterion,
 * >
 */
abstract class AbstractCompositeCriterionMapper implements CriterionMapperInterface
{
    /**
     * @return class-string<\Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\AbstractCompositeCriterion>
     */
    abstract protected function getHandledClass(): string;

    /**
     * @phpstan-return \Doctrine\Common\Collections\Expr\CompositeExpression::TYPE_*
     */
    abstract protected function getType(): string;

    final public function canHandle(CriterionInterface $criterion): bool
    {
        $handledClass = $this->getHandledClass();

        return $criterion instanceof $handledClass;
    }

    final public function handle(CriterionInterface $criterion, CriterionMapper $mapper): CompositeExpression
    {
        $expressions = $this->getExpressions($criterion->getCriteria(), $mapper);

        return new CompositeExpression($this->getType(), $expressions);
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface> $criteria
     *
     * @return array<\Doctrine\Common\Collections\Expr\Expression>
     */
    private function getExpressions(iterable $criteria, CriterionMapper $mapper): array
    {
        $expressions = [];
        foreach ($criteria as $criterion) {
            $expressions[] = $mapper->handle($criterion);
        }

        return $expressions;
    }
}
