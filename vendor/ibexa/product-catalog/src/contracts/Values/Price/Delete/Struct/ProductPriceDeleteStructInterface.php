<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct;

use Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;

interface ProductPriceDeleteStructInterface extends ProductPriceStructInterface
{
    public function getPrice(): PriceInterface;

    /**
     * @deprecated since 4.2. Use getPrice()->getId() instead.
     */
    public function getId(): int;
}
