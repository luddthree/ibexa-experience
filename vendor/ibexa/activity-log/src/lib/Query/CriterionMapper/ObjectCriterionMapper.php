<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Query\CriterionMapper;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Ibexa\Contracts\ActivityLog\CriterionMapperInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ObjectCriterion;

/**
 * @implements \Ibexa\Contracts\ActivityLog\CriterionMapperInterface<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ObjectCriterion>
 */
final class ObjectCriterionMapper implements CriterionMapperInterface
{
    public function canHandle(CriterionInterface $criterion): bool
    {
        return $criterion instanceof ObjectCriterion;
    }

    public function handle(CriterionInterface $criterion): Expression
    {
        if ($criterion->ids === null) {
            return new Comparison('log_record.object_class.object_class', Comparison::EQ, $criterion->objectClass);
        }

        return new CompositeExpression(
            CompositeExpression::TYPE_AND,
            [
                new Comparison('log_record.object_class.object_class', Comparison::EQ, $criterion->objectClass),
                new Comparison('log_record.object_id', Comparison::IN, $criterion->ids),
            ],
        );
    }
}
