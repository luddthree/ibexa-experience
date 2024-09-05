<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;

interface ValueFormatterInterface
{
    /**
     * @param array<string,mixed> $parameters
     */
    public function formatValue(AttributeInterface $attribute, array $parameters = []): ?string;
}
