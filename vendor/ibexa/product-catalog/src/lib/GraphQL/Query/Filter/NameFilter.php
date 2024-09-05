<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Query\Filter;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductName;

/**
 * @internal
 */
final class NameFilter implements FilterInterface
{
    public function getName(): string
    {
        return 'name';
    }

    public function getType(): string
    {
        return 'String';
    }

    public function getDescription(): string
    {
        return 'Filter on product name';
    }

    /**
     * @param array<mixed> $arguments
     */
    public function getCriterion(array $arguments): ProductName
    {
        return new ProductName(...$arguments);
    }
}
