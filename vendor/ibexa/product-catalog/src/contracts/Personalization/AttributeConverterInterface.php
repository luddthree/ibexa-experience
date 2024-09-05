<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Personalization;

use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;

/**
 * @internal
 */
interface AttributeConverterInterface
{
    public function accept(AttributeInterface $attribute): bool;

    /**
     * @return scalar|array<scalar>
     */
    public function convert(AttributeInterface $attribute);
}
