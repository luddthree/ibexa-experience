<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use DateTimeInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class CreatedAt implements CriterionInterface
{
    private DateTimeInterface $createdAt;

    private string $operator;

    public function __construct(DateTimeInterface $createdAt, string $operator = Operator::EQ)
    {
        $this->createdAt = $createdAt;
        $this->operator = $operator;
    }

    public function getValue(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }
}
