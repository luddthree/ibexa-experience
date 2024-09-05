<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class ProductVariantUpdateStruct extends ValueObject
{
    /** @var array<string,mixed> */
    private array $attributes;

    private ?string $code;

    /**
     * @param array<string,mixed> $attributes
     */
    public function __construct(array $attributes = [], ?string $code = null)
    {
        parent::__construct();

        $this->attributes = $attributes;
        $this->code = $code;
    }

    /**
     * @return array<string,mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<string,mixed> $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }
}
