<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class ProductAvailability implements CriterionInterface
{
    private bool $available;

    public function __construct(bool $available = true)
    {
        $this->available = $available;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }
}
