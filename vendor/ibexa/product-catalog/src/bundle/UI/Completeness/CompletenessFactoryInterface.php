<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

interface CompletenessFactoryInterface
{
    public function createProductCompleteness(ProductInterface $product): CompletenessInterface;
}
