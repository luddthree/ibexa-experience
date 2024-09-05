<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Query\CriterionMapper;

use Doctrine\Common\Collections\Expr\Comparison;
use Ibexa\Contracts\ActivityLog\CriterionMapperInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ActionCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;

/**
 * @implements \Ibexa\Contracts\ActivityLog\CriterionMapperInterface<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ActionCriterion>
 */
final class ActionCriterionMapper implements CriterionMapperInterface
{
    public function canHandle(CriterionInterface $criterion): bool
    {
        return $criterion instanceof ActionCriterion;
    }

    public function handle(CriterionInterface $criterion): Comparison
    {
        return new Comparison('log_record.action.action', Comparison::IN, $criterion->actions);
    }
}
