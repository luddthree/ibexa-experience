<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\NumberFormatter;

use NumberFormatter;

interface NumberFormatterFactoryInterface
{
    public function createNumberFormatter(?string $locale = null): NumberFormatter;
}
