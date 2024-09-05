<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class NewFilterDefinitionSideMenu extends Component
{
    public function setFilterValues(string $inputFrom, string $inputTo): void
    {
        $this->getHTMLPage()->setTimeout(5)->findAll($this->getLocator('fieldsForm'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('filterLabel'), 'From'))
            ->find($this->getLocator('fieldInput'))
            ->setValue($inputFrom);

        $this->getHTMLPage()->setTimeout(5)->findAll($this->getLocator('fieldsForm'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('filterLabel'), 'To'))
            ->find($this->getLocator('fieldInput'))
            ->setValue($inputTo);
    }

    public function selectProductAvailabilityOption(string $filterType): void
    {
        $this->getHTMLPage()->setTimeout(5)->findAll($this->getLocator('radioButtonsSelection'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('filterLabel'), $filterType))
            ->find($this->getLocator('fieldInput'))
            ->click();
    }

    public function setProductCodeValue(string $codeValue): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('codeInput'))->setValue($codeValue);
    }

    public function back(): void
    {
        $this->getHTMLPage()->find($this->getLocator('backButton'))->click();
    }

    public function confirm(): void
    {
        $this->getHTMLPage()->find($this->getLocator('saveButton'))->click();
    }

    public function decline(): void
    {
        $this->getHTMLPage()->find($this->getLocator('cancelButton'))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('sideMenu'))->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('sideMenu', '.ibexa-pc-edit-config-filter:not(.ibexa-pc-config-panel--hidden)'),
            new VisibleCSSLocator('fieldsForm', '.ibexa-pc-edit-config-number-range-filter__field'),
            new VisibleCSSLocator('filterLabel', '.ibexa-label'),
            new VisibleCSSLocator('fieldInput', '.ibexa-input'),
            new VisibleCSSLocator('radioButtonsSelection', '.form-check'),
            new VisibleCSSLocator('codeInput', '.taggify__input'),
            new VisibleCSSLocator('backButton', '.ibexa-btn--close-config-panel'),
            new VisibleCSSLocator('saveButton', '.ibexa-pc-config-panel__save-btn'),
            new VisibleCSSLocator('cancelButton', '.ibexa-pc-config-panel__cancel-btn'),
        ];
    }
}
