<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ElementAttributeCriterion;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class LeftMenu extends Component
{
    public function goToTab(string $tabName): void
    {
        $menuButton = $this->getHTMLPage()
            ->findAll($this->getLocator('menuItem'))
            ->getByCriterion(new ElementAttributeCriterion('data-original-title', $tabName));
        $menuButton->click();
        $menuButton->find(new VisibleCSSLocator('activeMarker', '.ibexa-main-menu__item-action.active'))->assert()->isVisible();
    }

    public function goToSubTab(string $tabName): void
    {
        $this->getHTMLPage()->find($this->getLocator('menuSecondLevel'))->mouseOver();

        $this->getHTMLPage()
            ->findAll($this->getLocator('expandedMenuItem'))
            ->getByCriterion(new ElementTextCriterion($tabName))
            ->click();
    }

    public function toggleMenu(): void
    {
        $this->getHTMLPage()->find($this->getLocator('menuToggler'))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('menuSelector'))->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('menuItem', '.ibexa-main-menu__navbar--first-level .ibexa-main-menu__item'),
            new VisibleCSSLocator('expandedMenuItem', '.ibexa-main-menu__navbar--second-level .ibexa-main-menu__tab-pane.active.show .ibexa-main-menu__item-text-column'),
            new VisibleCSSLocator('menuSelector', '.ibexa-main-menu'),
            new VisibleCSSLocator('menuFirstLevel', '.ibexa-main-menu__navbar--first-level'),
            new VisibleCSSLocator('menuSecondLevel', '.ibexa-main-menu__navbar--second-level'),
            new VisibleCSSLocator('menuToggler', '.ibexa-main-menu__toggler'),
        ];
    }
}
