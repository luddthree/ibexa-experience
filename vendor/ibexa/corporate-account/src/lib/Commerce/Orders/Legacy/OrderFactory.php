<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Commerce\Orders\Legacy;

use Ibexa\Bundle\Commerce\Checkout\Entity\Basket;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\CorporateAccount\Commerce\Orders\OrderInterface;

/**
 * @internal
 */
final class OrderFactory implements OrderFactoryInterface
{
    private MemberService $memberService;

    private InvoiceFactoryInterface $invoiceFactory;

    public function __construct(MemberService $memberService, InvoiceFactoryInterface $invoiceFactory)
    {
        $this->memberService = $memberService;
        $this->invoiceFactory = $invoiceFactory;
    }

    public function createFromBasket(Company $company, Basket $basket): OrderInterface
    {
        return new Order(
            $basket,
            $this->memberService->getMember($basket->getUserId(), $company),
            $this->invoiceFactory->createInvoiceForBasket($basket)
        );
    }
}
