<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

interface ProductCodeDataContainerInterface
{
    public function getCode(): ?string;

    public function getOriginalCode(): ?string;
}
