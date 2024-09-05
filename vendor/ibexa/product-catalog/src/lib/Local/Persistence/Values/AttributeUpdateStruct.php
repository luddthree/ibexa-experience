<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

final class AttributeUpdateStruct
{
    public int $id;

    public string $discriminator;

    /** @var object|scalar|array<mixed>|null */
    public $value;

    /**
     * @param object|scalar|array<mixed>|null $value
     */
    public function __construct(int $id, string $discriminator, $value)
    {
        $this->id = $id;
        $this->discriminator = $discriminator;
        $this->value = $value;
    }
}
