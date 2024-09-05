<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Price\Create;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\CustomerGroupAwareInterface;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\CustomerGroupAwareTrait;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class CustomerGroupPriceCreateData extends AbstractProductPriceCreateData implements CustomerGroupAwareInterface
{
    use CustomerGroupAwareTrait;

    public function __construct(
        ProductInterface $product,
        CurrencyInterface $currency,
        CustomerGroupInterface $customerGroup
    ) {
        parent::__construct($product, $currency);
        $this->customerGroup = $customerGroup;
    }
}
