<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Commerce\Orders\Legacy;

use Ibexa\Bundle\Commerce\Checkout\Entity\Basket;
use Ibexa\CorporateAccount\Commerce\Orders\InvoiceInterface;

/**
 * @internal
 */
interface InvoiceFactoryInterface
{
    public function createInvoiceForBasket(Basket $basket): ?InvoiceInterface;
}
