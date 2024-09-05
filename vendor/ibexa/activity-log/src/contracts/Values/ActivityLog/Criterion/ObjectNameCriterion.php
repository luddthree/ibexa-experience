<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion;

use Doctrine\Common\Collections\Expr\Comparison;

final class ObjectNameCriterion implements CriterionInterface
{
    public const OPERATOR_CONTAINS = Comparison::CONTAINS;
    public const OPERATOR_STARTS_WITH = Comparison::STARTS_WITH;
    public const OPERATOR_ENDS_WITH = Comparison::ENDS_WITH;
    public const OPERATOR_EQUALS = Comparison::EQ;

    public string $query;

    public string $operator;

    public function __construct(string $query, string $operator)
    {
        $this->query = $query;
        $this->operator = $operator;
    }
}
