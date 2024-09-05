<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Ibexa\AdminUi\Behat\Page\AdminUpdateItemPage;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class ProductPriceUpdatePage extends AdminUpdateItemPage
{
    public function setBasePrice(int $basePriceValue): void
    {
        $this->getHTMLPage()->find($this->getLocator('basePriceField'))->setValue($basePriceValue);
    }

    protected function specifyLocators(): array
    {
        return array_merge(
            [
                new VisibleCSSLocator('basePriceField', '[name="product_prices[price][base_price]"]'),
            ],
            parent::specifyLocators()
        );
    }
}
