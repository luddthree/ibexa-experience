<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Query\CriterionMapper;

use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Ibexa\Contracts\ActivityLog\CriterionMapperInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LogicalNot;

/**
 * @implements \Ibexa\Contracts\ActivityLog\CriterionMapperInterface<
 *     \Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LogicalNot,
 * >
 */
final class LogicalNotCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-var \Ibexa\Contracts\ActivityLog\CriterionMapperInterface<
     *     \Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface,
     * >
     */
    private CriterionMapperInterface $criterionMapper;

    /**
     * @phpstan-param \Ibexa\Contracts\ActivityLog\CriterionMapperInterface<
     *     \Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface,
     * > $criterionMapper
     */
    public function __construct(CriterionMapperInterface $criterionMapper)
    {
        $this->criterionMapper = $criterionMapper;
    }

    public function canHandle(CriterionInterface $criterion): bool
    {
        return $criterion instanceof LogicalNot;
    }

    public function handle(CriterionInterface $criterion): Expression
    {
        return new CompositeExpression(
            'NOT',
            [$this->criterionMapper->handle($criterion->criterion)],
        );
    }
}
