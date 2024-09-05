<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\REST\Output;

use Ibexa\Bundle\ProductCatalog\REST\Value\Attribute;

interface AttributePostProcessorInterface
{
    public function supports(Attribute $attribute): bool;

    /**
     * @return array<string, mixed>
     */
    public function process(Attribute $attribute): array;
}
