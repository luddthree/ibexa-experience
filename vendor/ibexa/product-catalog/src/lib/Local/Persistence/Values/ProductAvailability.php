<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

use Ibexa\Contracts\Core\Persistence\ValueObject;

final class ProductAvailability extends ValueObject
{
    protected int $id;

    protected bool $availability;

    protected bool $isInfinite;

    protected string $productCode;

    protected ?int $stock;

    public function __construct(
        int $id,
        bool $availability,
        bool $isInfinite,
        ?int $stock,
        string $productCode
    ) {
        parent::__construct();
        $this->id = $id;
        $this->availability = $availability;
        $this->stock = $stock;
        $this->productCode = $productCode;
        $this->isInfinite = $isInfinite;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isAvailable(): bool
    {
        return $this->availability;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function isInfinite(): bool
    {
        return $this->isInfinite;
    }
}
