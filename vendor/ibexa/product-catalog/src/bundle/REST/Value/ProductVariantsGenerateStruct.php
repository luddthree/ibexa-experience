<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class ProductVariantsGenerateStruct extends ValueObject
{
    /** @var array<string,array<mixed>> */
    private array $attributes;

    /**
     * @param array<string,array<mixed>> $attributes
     */
    public function __construct(array $attributes)
    {
        parent::__construct();

        $this->attributes = $attributes;
    }

    /**
     * @return array<string,array<mixed>>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
