<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion;

final class UserCriterion implements CriterionInterface
{
    /** @var int[] */
    public array $userIds;

    /**
     * @param array<int> $userIds
     */
    public function __construct(array $userIds)
    {
        $this->userIds = $userIds;
    }
}
