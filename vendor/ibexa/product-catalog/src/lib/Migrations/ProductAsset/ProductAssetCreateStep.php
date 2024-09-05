<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductAsset;

use Ibexa\Migration\ValueObject\Step\StepInterface;

final class ProductAssetCreateStep implements StepInterface
{
    private string $productCode;

    private string $uri;

    /** @var array<string, mixed> */
    private array $tags;

    /**
     * @param array<string, mixed> $tags
     */
    public function __construct(string $productCode, string $uri, array $tags)
    {
        $this->productCode = $productCode;
        $this->uri = $uri;
        $this->tags = $tags;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}
