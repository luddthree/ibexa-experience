<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;

interface ValueFormatterDispatcherInterface
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function formatValue(AttributeInterface $attribute, array $parameters = []): ?string;
}
