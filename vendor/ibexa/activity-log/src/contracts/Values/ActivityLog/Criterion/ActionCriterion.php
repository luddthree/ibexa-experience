<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion;

final class ActionCriterion implements CriterionInterface
{
    /** @var array<string> */
    public array $actions;

    /**
     * @param array<string> $actions
     */
    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }
}
