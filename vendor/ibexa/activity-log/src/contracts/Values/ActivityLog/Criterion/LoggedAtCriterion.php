<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion;

use DateTimeInterface;

final class LoggedAtCriterion implements CriterionInterface
{
    public const EQ = '=';
    public const NEQ = '<>';
    public const LT = '<';
    public const LTE = '<=';
    public const GT = '>';
    public const GTE = '>=';

    public DateTimeInterface $dateTime;

    public string $operator;

    /**
     * @param string $comparison
     */
    public function __construct(DateTimeInterface $dateTime, string $comparison)
    {
        $this->dateTime = $dateTime;
        $this->operator = $comparison;
    }
}
