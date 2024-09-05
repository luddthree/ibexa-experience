<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

use Ibexa\Contracts\Core\Persistence\ValueObject;

abstract class AbstractProduct extends ValueObject
{
    public int $id;

    public string $code;

    /** @var \Ibexa\ProductCatalog\Local\Persistence\Values\Attribute[] */
    public array $attributes = [];
}
