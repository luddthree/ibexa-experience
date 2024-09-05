<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Query\CriterionMapper;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\Expression;
use Ibexa\Contracts\ActivityLog\CriterionMapperInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\UserCriterion;

/**
 * @implements \Ibexa\Contracts\ActivityLog\CriterionMapperInterface<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\UserCriterion>
 */
final class UserCriterionMapper implements CriterionMapperInterface
{
    public function canHandle(CriterionInterface $criterion): bool
    {
        return $criterion instanceof UserCriterion;
    }

    public function handle(CriterionInterface $criterion): Expression
    {
        return new Comparison('user_id', Comparison::IN, $criterion->userIds);
    }
}
