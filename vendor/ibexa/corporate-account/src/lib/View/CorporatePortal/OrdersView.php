<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View\CorporatePortal;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\MVC\Symfony\View\BaseView;

class OrdersView extends BaseView
{
    /** @var iterable<\Ibexa\Bundle\Commerce\Checkout\Entity\Basket> */
    private iterable $orders;

    private Company $company;

    /**
     * @param iterable<\Ibexa\Bundle\Commerce\Checkout\Entity\Basket> $orders
     */
    public function __construct(
        string $templateIdentifier,
        iterable $orders,
        Company $company
    ) {
        parent::__construct($templateIdentifier);

        $this->orders = $orders;
        $this->company = $company;
    }

    /**
     * @return array{
     *     orders: iterable<\Ibexa\Bundle\Commerce\Checkout\Entity\Basket>,
     *     company: \Ibexa\Contracts\CorporateAccount\Values\Company
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'orders' => $this->orders,
            'company' => $this->company,
        ];
    }

    /**
     * @return iterable<\Ibexa\Bundle\Commerce\Checkout\Entity\Basket>
     */
    public function getOrders(): iterable
    {
        return $this->orders;
    }

    /**
     * @param iterable<\Ibexa\Bundle\Commerce\Checkout\Entity\Basket> $orders
     */
    public function setOrders(iterable $orders): void
    {
        $this->orders = $orders;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }
}
