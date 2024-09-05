<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Values\VatCategory\VatCategoryListInterface;
use Traversable;

final class VatCategoryList implements VatCategoryListInterface
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface[] */
    private array $vatCategories;

    private int $totalCount;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface[] $vatCategories
     */
    public function __construct(array $vatCategories, int $totalCount)
    {
        $this->vatCategories = $vatCategories;
        $this->totalCount = $totalCount;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->vatCategories);
    }

    public function count(): int
    {
        return count($this->vatCategories);
    }

    public function getVatCategories(): array
    {
        return $this->vatCategories;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
