<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Money\Money;

final class ProductPriceUpdateStruct extends ValueObject implements ProductPriceUpdateStructInterface
{
    private PriceInterface $price;

    private ?Money $money;

    private ?Money $customPriceMoney;

    private ?string $customPriceRule;

    public function __construct(
        PriceInterface $price,
        ?Money $money = null,
        ?Money $customPriceMoney = null,
        ?string $customPriceRule = null
    ) {
        parent::__construct();

        $this->price = $price;
        $this->money = $money;
        $this->customPriceMoney = $customPriceMoney;
        $this->customPriceRule = $customPriceRule;
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
            'Method Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStruct::getId is deprecated since 4.2 and will be removed in 5.0. Use getPrice()->getId()',
            E_USER_DEPRECATED
        );

        return $this->price->getId();
    }

    public function getMoney(): ?Money
    {
        return $this->money;
    }

    public function getCustomPriceMoney(): ?Money
    {
        return $this->customPriceMoney;
    }

    public function getCustomPriceRule(): ?string
    {
        return $this->customPriceRule;
    }

    public function execute(ProductPriceServiceInterface $productPriceService): void
    {
        $productPriceService->updateProductPrice($this);
    }

    public function getProduct(): ProductInterface
    {
        return $this->price->getProduct();
    }
}
