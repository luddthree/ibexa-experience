<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class PageBuilderVersionPopup extends Component
{
    public function getLabels(int $versionNumber): array
    {
        $labelsText = $this->getHTMLPage()->findAll($this->getLocator('tableRow'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('versionNo'), (string) $versionNumber))
            ->find($this->getLocator('labels'))
            ->getText();

        $labels = preg_replace('/\s+/', ',', $labelsText);
        $labels = strtolower($labels);

        return explode(',', $labels);
    }

    public function preview(int $versionNumber): void
    {
        $this->getHTMLPage()->findAll($this->getLocator('tableRow'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('versionNo'), (string) $versionNumber))
            ->find($this->getLocator('preview'))
            ->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(3)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('modal')))
            ->find($this->getLocator('title'))
            ->assert()->textContains('Versions:');
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('tableRow', '.ibexa-table--versions tbody tr'),
            new VisibleCSSLocator('versionNo', 'td:nth-of-type(2)'),
            new VisibleCSSLocator('labels', 'td:nth-of-type(1)'),
            new VisibleCSSLocator('preview', '.ibexa-icon--view'),
            new VisibleCSSLocator('modal', '.ibexa-modal--versions.show'),
            new VisibleCSSLocator('title', '.ibexa-modal--versions .modal-title'),
        ];
    }
}
