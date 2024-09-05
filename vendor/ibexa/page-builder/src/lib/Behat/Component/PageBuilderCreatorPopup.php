<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class PageBuilderCreatorPopup extends Component
{
    public function chooseLayout(string $layout): void
    {
        usleep(100000);
        $layoutLocator = new VisibleCSSLocator('layoutLocator', sprintf($this->getLocator('layoutFormat')->getSelector(), $layout));
        $this->getHTMLPage()->find($layoutLocator)->click();
        $this->getHTMLPage()->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('selectedLayout')));
        Assert::assertEquals($layout, $this->getHTMLPage()->find($this->getLocator('selectedLayout'))->getAttribute('data-id'));
    }

    public function finishCreating(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('backgroundFade'))->assert()->isVisible();
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('visiblePopupSelector'))->assert()->isVisible();
        $this->getHTMLPage()->find($this->getLocator('popupTitle'))->assert()->textEquals('Select layout');
        $this->getHTMLPage()->find($this->getLocator('createButton'))->assert()->textEquals('Select');
        $this->getHTMLPage()->find($this->getLocator('closeButton'))->assert()->textEquals('Discard');
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('layoutFormat', '.c-pb-layout-selector__item[data-id="%s"]'),
            new VisibleCSSLocator('createButton', '.c-popup__footer .ibexa-btn--filled-info'),
            new VisibleCSSLocator('closeButton', '.c-popup__footer .ibexa-btn--info'),
            new VisibleCSSLocator('visiblePopupSelector', '.c-pb-layout-selector__popup.show'),
            new VisibleCSSLocator('selectedLayout', '.c-pb-layout-selector__item--selected'),
            new VisibleCSSLocator('backgroundFade', '.modal-backdrop.show'),
            new VisibleCSSLocator('popupTitle', '.c-popup__title'),
        ];
    }

    public function isVisible(): bool
    {
        return $this->context->isElementVisible($this->fields['visiblePopupSelector']);
    }
}
