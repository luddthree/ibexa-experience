<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

interface TranslatableInterface
{
    /**
     * @return array<int,string> List of language codes
     */
    public function getLanguages(): array;
}
