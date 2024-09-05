<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Money\Money;

abstract class AbstractPriceCreateStruct extends ValueObject implements ProductPriceCreateStructInterface
{
    private ProductInterface $product;

    private CurrencyInterface $currency;

    private Money $money;

    private ?Money $customPriceMoney;

    /**
     * @var numeric-string|null
     */
    private ?string $customPriceRule;

    /**
     * @param numeric-string|null $customPriceRule
     */
    public function __construct(
        ProductInterface $product,
        CurrencyInterface $currency,
        Money $money,
        ?Money $customPriceMoney = null,
        ?string $customPriceRule = null
    ) {
        parent::__construct();

        $this->product = $product;
        $this->currency = $currency;
        $this->money = $money;
        $this->customPriceMoney = $customPriceMoney;
        $this->customPriceRule = $customPriceRule;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    /**
     * @return numeric-string
     */
    public function getAmount(): string
    {
        return $this->money->getAmount();
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }

    public function getCustomPrice(): ?Money
    {
        return $this->customPriceMoney;
    }

    /**
     * @return numeric-string|null
     */
    public function getCustomPriceRule(): ?string
    {
        return $this->customPriceRule;
    }

    public function execute(ProductPriceServiceInterface $productPriceService): void
    {
        $productPriceService->createProductPrice($this);
    }
}
