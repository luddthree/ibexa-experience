<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Commerce\Orders\Legacy;

use DateTimeInterface;
use Ibexa\Bundle\Commerce\Checkout\Entity\Basket;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\CorporateAccount\Commerce\Orders\InvoiceInterface;
use Ibexa\CorporateAccount\Commerce\Orders\OrderInterface;
use Money\Currency;
use Money\Money;

final class Order implements OrderInterface
{
    private Basket $basket;

    private Member $member;

    private ?InvoiceInterface $invoice;

    public function __construct(Basket $basket, Member $member, ?InvoiceInterface $invoice)
    {
        $this->basket = $basket;
        $this->member = $member;
        $this->invoice = $invoice;
    }

    public function getId(): string
    {
        return (string)$this->basket->getBasketId();
    }

    public function getDate(): DateTimeInterface
    {
        return $this->basket->getDateLastModified();
    }

    public function getTotal(): Money
    {
        $sum = $this->basket->getTotalsSum();
        /** @var non-empty-string $code */
        $code = $sum->getCurrency();

        return new Money(
            (string)$sum->getTotalGross(),
            new Currency($code)
        );
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function getShippingMethod(): string
    {
        return $this->basket->getShippingMethod();
    }

    public function getState(): string
    {
        return $this->basket->getState();
    }

    public function getBasket(): Basket
    {
        return $this->basket;
    }

    public function getInvoice(): ?InvoiceInterface
    {
        return $this->invoice;
    }
}
