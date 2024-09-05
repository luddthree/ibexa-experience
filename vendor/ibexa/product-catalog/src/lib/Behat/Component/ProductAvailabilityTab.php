<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

final class ProductAvailabilityTab extends Component
{
    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('tab'))->assert()->isVisible();
    }

    public function addAvailability(): void
    {
        $this->getHTMLPage()->find($this->getLocator('addAvailabilityButton'))->click();
    }

    public function verifyStock(int $expectedStockValue): void
    {
        Assert::assertEquals(
            $expectedStockValue,
            (int)$this->getHTMLPage()->find($this->getLocator('stockPreview'))->getText(),
            'Field has wrong value'
        );
    }

    public function verifyAvailability(bool $expectedAvailabilityValue): void
    {
        $isAvailable = $this->getHTMLPage()
            ->find($this->getLocator('availableBadge'))
            ->hasClass($this->getLocator('isAvailable')->getSelector());

        Assert::assertEquals($expectedAvailabilityValue, $isAvailable);
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('tab', '#ibexa-tab-product-availability'),
            new VisibleCSSLocator('addAvailabilityButton', '.ibexa-table-header__actions'),
            new VisibleCSSLocator('stockPreview', '.ibexa-pc-product-availability__table td:nth-child(2)'),
            new VisibleCSSLocator('availableBadge', '.ibexa-badge--status'),
            new VisibleCSSLocator('isAvailable', 'ibexa-badge--success'),
        ];
    }
}
