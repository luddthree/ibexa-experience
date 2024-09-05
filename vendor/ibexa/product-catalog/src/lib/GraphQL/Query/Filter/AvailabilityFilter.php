<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Query\Filter;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability;

/**
 * @internal
 */
final class AvailabilityFilter implements FilterInterface
{
    public function getName(): string
    {
        return 'availability';
    }

    public function getType(): string
    {
        return 'ProductAvailability';
    }

    public function getDescription(): string
    {
        return 'Filter on product availability';
    }

    /**
     * @param array<mixed> $arguments
     */
    public function getCriterion(array $arguments): ProductAvailability
    {
        return new ProductAvailability(...$arguments);
    }
}
