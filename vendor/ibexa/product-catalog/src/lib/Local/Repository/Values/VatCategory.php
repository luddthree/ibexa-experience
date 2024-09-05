<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;

final class VatCategory implements VatCategoryInterface
{
    private string $identifier;

    private string $region;

    private ?float $vatValue;

    public function __construct(string $region, string $identifier, ?float $vatValue)
    {
        $this->region = $region;
        $this->identifier = $identifier;
        $this->vatValue = $vatValue;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getVatValue(): ?float
    {
        return $this->vatValue;
    }
}
