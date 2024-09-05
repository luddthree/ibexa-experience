<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Action\MouseOverAndClick;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementTransitionHasEndedCondition;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class PageBuilderIntroductionPopup extends Component
{
    public function closeIntroductoryModal(): void
    {
        $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('continueButton')));

        $this->getHTMLPage()->setTimeout(10)
            ->waitUntilCondition(new ElementTransitionHasEndedCondition($this->getHTMLPage(), $this->getLocator('visiblePopupSelector')));
        $this->getHTMLPage()->find($this->getLocator('continueButton'))->execute(new MouseOverAndClick());

        $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('continueButton')));
        $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('backgroundFade')));
        $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('visiblePopupSelector')));
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('backgroundFade'))->assert()->isVisible();
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('visiblePopupSelector'))->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('visiblePopupSelector', '.modal-dialog.c-popup__dialog'),
            new VisibleCSSLocator('backgroundFade', '.modal-backdrop.show'),
            new VisibleCSSLocator('continueButton', '.modal-footer .btn.ibexa-btn.ibexa-btn--filled-info'),
            new VisibleCSSLocator('changeSettingsButton', ' .modal-footer .btn.ibexa-btn.ibexa-btn--info'),
        ];
    }
}
