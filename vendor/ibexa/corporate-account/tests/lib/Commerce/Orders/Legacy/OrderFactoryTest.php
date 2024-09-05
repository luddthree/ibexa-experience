<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\Commerce\Orders\Legacy;

use Ibexa\Bundle\Commerce\Checkout\Entity\Basket;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\CorporateAccount\Commerce\Orders\InvoiceInterface;
use Ibexa\CorporateAccount\Commerce\Orders\Legacy\InvoiceFactoryInterface;
use Ibexa\CorporateAccount\Commerce\Orders\Legacy\Order;
use Ibexa\CorporateAccount\Commerce\Orders\Legacy\OrderFactory;
use PHPUnit\Framework\TestCase;

final class OrderFactoryTest extends TestCase
{
    private const EXAMPLE_USER_ID = 23;

    /** @var \Ibexa\Contracts\CorporateAccount\Service\MemberService&\PHPUnit\Framework\MockObject\MockObject */
    private MemberService $memberService;

    /** @var \Ibexa\CorporateAccount\Commerce\Orders\Legacy\InvoiceFactoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private InvoiceFactoryInterface $invoiceFactory;

    private OrderFactory $orderFactory;

    protected function setUp(): void
    {
        $this->memberService = $this->createMock(MemberService::class);
        $this->invoiceFactory = $this->createMock(InvoiceFactoryInterface::class);
        $this->orderFactory = new OrderFactory($this->memberService, $this->invoiceFactory);
    }

    public function testCreateFromBasket(): void
    {
        $company = new Company($this->createMock(Content::class));
        $member = $this->createExampleMember($company);
        $basket = $this->createBasketOwnedByUser(self::EXAMPLE_USER_ID);
        $invoice = $this->createMock(InvoiceInterface::class);

        $this->invoiceFactory
            ->method('createInvoiceForBasket')
            ->with($basket)
            ->willReturn($invoice);

        $this->memberService
            ->method('getMember')
            ->with(self::EXAMPLE_USER_ID)
            ->willReturn($member);

        self::assertEquals(
            new Order($basket, $member, $invoice),
            $this->orderFactory->createFromBasket($company, $basket)
        );
    }

    private function createBasketOwnedByUser(int $userId): Basket
    {
        $basket = $this->createMock(Basket::class);
        $basket
            ->method('getUserId')
            ->willReturn($userId);

        return $basket;
    }

    private function createExampleMember(Company $company): Member
    {
        return new Member(
            $this->createMock(User::class),
            $company,
            $this->createMock(Role::class)
        );
    }
}
