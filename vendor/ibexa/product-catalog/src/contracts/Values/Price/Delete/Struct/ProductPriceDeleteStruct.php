<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class ProductPriceDeleteStruct extends ValueObject implements ProductPriceDeleteStructInterface
{
    private PriceInterface $price;

    public function __construct(PriceInterface $price)
    {
        parent::__construct();
        $this->price = $price;
    }

    public function getPrice(): PriceInterface
    {
        return $this->price;
    }

    /**
     * @deprecated since 4.2. Use getPrice()->getId() instead.
     */
    public function getId(): int
    {
        @trigger_error(
            'Method Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceDeleteStruct::getId is deprecated since 4.2 and will be removed in 5.0. Use getPrice()->getId()',
            E_USER_DEPRECATED
        );

        return $this->price->getId();
    }

    public function execute(ProductPriceServiceInterface $productPriceService): void
    {
        $productPriceService->deletePrice($this);
    }

    public function getProduct(): ProductInterface
    {
        return $this->price->getProduct();
    }
}
