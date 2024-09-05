<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class Visibility extends Component
{
    public function selectSegmentUnderSegmentGroup(string $segmentName): void
    {
        $this->getHTMLPage()->setTimeout(3)->find($this->getLocator('visibilityFilterInput'))->setValue($segmentName);
        $this->getHTMLPage()->setTimeout(10)->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('visibilitySegmentListInput')))
            ->find($this->getLocator('visibilitySegmentListInput'))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('visibilityPanel'))
            ->assert()->isVisible()
            ->find($this->getLocator('visibilityHeader'))
            ->assert()->textEquals('Segments');
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('visibilityPanel', '.ibexa-pb-config-panel--visibility'),
            new VisibleCSSLocator('visibilityHeader', '.ibexa-pb-config-panel__title'),
            new VisibleCSSLocator('visibilityFilterInput', '.c-segments__search-bar input'),
            new VisibleCSSLocator('visibilitySegmentListInput', '.c-segments__list li:not(.c-segments__item--hidden) .c-segments__label input'),
        ];
    }
}
