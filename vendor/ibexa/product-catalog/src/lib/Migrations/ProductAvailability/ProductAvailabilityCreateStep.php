<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductAvailability;

use Ibexa\Migration\ValueObject\Step\StepInterface;

final class ProductAvailabilityCreateStep implements StepInterface
{
    /** @phpstan-var non-empty-string */
    private string $productCode;

    /** @phpstan-var int<0, max>|null */
    private ?int $stock;

    private bool $availability;

    private bool $isInfinite;

    /**
     * @phpstan-param non-empty-string $productCode
     * @phpstan-param int<0, max>|null $stock
     */
    public function __construct(
        string $productCode,
        ?int $stock,
        bool $availability,
        bool $isInfinite
    ) {
        $this->productCode = $productCode;
        $this->stock = $stock;
        $this->availability = $availability;
        $this->isInfinite = $isInfinite;
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function getProductCode(): string
    {
        return $this->productCode;
    }

    /**
     * @phpstan-return int<0, max>
     */
    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function isAvailable(): bool
    {
        return $this->availability;
    }

    public function isInfinite(): bool
    {
        return $this->isInfinite;
    }
}
