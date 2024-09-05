<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class ProductVariantCreateStruct extends ValueObject
{
    /** @var array<int|string,mixed> */
    private array $attributes;

    private string $code;

    /**
     * @param array<int|string,mixed> $attributes
     */
    public function __construct(array $attributes, string $code)
    {
        parent::__construct();

        $this->code = $code;
        $this->attributes = $attributes;
    }

    /**
     * @return array<int|string,mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
