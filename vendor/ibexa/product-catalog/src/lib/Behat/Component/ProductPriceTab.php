<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Component;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class ProductPriceTab extends Component
{
    private IbexaDropdown $ibexaDropdown;

    public function __construct(Session $session, IbexaDropdown $ibexaDropdown)
    {
        parent::__construct($session);
        $this->ibexaDropdown = $ibexaDropdown;
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('tab'))->assert()->isVisible();
    }

    public function addPrice(): void
    {
        $this->getHTMLPage()->find($this->getLocator('addPriceButton'))->click();
    }

    public function verifyBasePrice(string $expectedBasePriceValue): void
    {
        $this->getHTMLPage()->find($this->getLocator('basePricePreview'))->assert()->textEquals($expectedBasePriceValue);
    }

    public function selectCurrency(string $currency): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('dropdown'))
            ->click();

        $this->ibexaDropdown->verifyIsLoaded();
        $this->ibexaDropdown->selectOption($currency);
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('tab', '#ibexa-tab-product-prices'),
            new VisibleCSSLocator('addPriceButton', '.ibexa-pc-product-prices__actions-edit'),
            new VisibleCSSLocator('basePricePreview', '.ibexa-pc-product-prices__main-price .ibexa-pc-product-item-preview__value'),
            new VisibleCSSLocator('dropdown', '.ibexa-pc-product-prices .ibexa-dropdown__wrapper .ibexa-dropdown__selection-info'),
        ];
    }
}
