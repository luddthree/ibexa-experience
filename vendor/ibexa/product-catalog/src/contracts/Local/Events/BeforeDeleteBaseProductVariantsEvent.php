<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class BeforeDeleteBaseProductVariantsEvent extends BeforeEvent
{
    private ProductInterface $baseProduct;

    public function __construct(ProductInterface $baseProduct)
    {
        $this->baseProduct = $baseProduct;
    }

    public function getBaseProduct(): ProductInterface
    {
        return $this->baseProduct;
    }
}
