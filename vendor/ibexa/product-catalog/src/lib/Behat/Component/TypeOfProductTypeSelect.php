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

final class TypeOfProductTypeSelect extends Component
{
    private IbexaDropdown $ibexaDropdown;

    public function __construct(Session $session, IbexaDropdown $ibexaDropdown)
    {
        parent::__construct($session);
        $this->ibexaDropdown = $ibexaDropdown;
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('overlayContainer'))->assert()->isVisible();
    }

    public function selectType(string $typeOfProductType): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('dropdown'))
            ->click();

        $this->ibexaDropdown->verifyIsLoaded();
        $this->ibexaDropdown->selectOption($typeOfProductType);
    }

    public function confirm(): void
    {
        $this->getHTMLPage()->find($this->getLocator('addButton'))->click();
    }

    public function cancel(): void
    {
        $this->getHTMLPage()->find($this->getLocator('cancelButton'))->click();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('overlayContainer', '.ibexa-extra-actions--product'),
            new VisibleCSSLocator('dropdown', '.ibexa-extra-actions__content .ibexa-dropdown__wrapper .ibexa-dropdown__selection-info'),
            new VisibleCSSLocator('addButton', '.ibexa-extra-actions__btns .ibexa-btn--primary'),
            new VisibleCSSLocator('cancelButton', '.ibexa-extra-actions__btns .ibexa-btn--close'),
        ];
    }
}
