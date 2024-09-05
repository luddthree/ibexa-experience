<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Query\Filter;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

/**
 * @internal
 */
interface FilterInterface
{
    public function getName(): string;

    public function getType(): string;

    public function getDescription(): string;

    /**
     * @param array<mixed> $arguments
     */
    public function getCriterion(array $arguments): CriterionInterface;
}
