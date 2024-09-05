<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Price\Update;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\CustomerGroupAwareInterface;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\CustomerGroupAwareTrait;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomPriceAwareInterface;

final class CustomerGroupPriceUpdateData extends AbstractProductPriceUpdateData implements CustomerGroupAwareInterface
{
    use CustomerGroupAwareTrait;

    public function __construct(CustomPriceAwareInterface $price, CustomerGroupInterface $customerGroup)
    {
        parent::__construct($price);
        $this->customerGroup = $customerGroup;

        $this->setCustomPrice($price->getCustomPriceAmount());
        $this->setCustomPriceRule($price->getCustomPriceRule());
    }
}
