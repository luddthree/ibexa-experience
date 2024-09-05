<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Ibexa\AdminUi\Behat\Page\AdminUpdateItemPage;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class ProductAvailabilityUpdatePage extends AdminUpdateItemPage
{
    public function setStock(int $stockValue): void
    {
        $this->getHTMLPage()->find($this->getLocator('stockField'))->setValue($stockValue);
    }

    public function setAvailability(bool $availabilityValue): void
    {
        if ($this->getAvailabilityValue() !== $availabilityValue) {
            $this->getHTMLPage()->find($this->getLocator('availabilityIndicator'))->click();
        }
    }

    public function getAvailabilityValue(): bool
    {
        return $this->getHTMLPage()->find($this->getLocator('availabilityField'))
            ->hasClass($this->getLocator('checked')->getSelector());
    }

    protected function specifyLocators(): array
    {
        return array_merge(
            [
                new VisibleCSSLocator('stockField', '[name="product_availability_create[stock]"]'),
                new VisibleCSSLocator('availabilityField', '.ibexa-toggle--checkbox'),
                new VisibleCSSLocator('availabilityIndicator', '.ibexa-toggle__indicator'),
                new VisibleCSSLocator('checked', 'ibexa-toggle--is-checked'),
            ],
            parent::specifyLocators()
        );
    }
}
