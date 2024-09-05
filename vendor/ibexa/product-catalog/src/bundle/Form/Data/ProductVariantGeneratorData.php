<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

final class ProductVariantGeneratorData
{
    /** @var array<string,array<mixed>> */
    private array $attributes = [];

    /**
     * @return array<string,array<mixed>>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<string,array<mixed>> $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }
}
