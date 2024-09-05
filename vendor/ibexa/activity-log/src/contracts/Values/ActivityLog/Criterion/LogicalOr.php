<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion;

final class LogicalOr extends CompositeCriterion
{
    /**
     * @param \Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface[] $criteria
     */
    public function __construct(array $criteria)
    {
        parent::__construct($criteria, self::TYPE_OR);
    }
}
