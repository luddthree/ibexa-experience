<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion;

final class LogicalNot implements CriterionInterface
{
    public CriterionInterface $criterion;

    public function __construct(CriterionInterface $criterion)
    {
        $this->criterion = $criterion;
    }
}
