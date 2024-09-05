<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Workflow\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class WorkflowPopup extends Component
{
    public function setMessage(string $message): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->find($this->getLocator('message_textarea'))
            ->setValue($message);
    }

    public function submit(): void
    {
        $this->getHTMLPage()->find($this->getLocator('submit_button'))->click();
    }

    public function selectReviewer(string $reviewer): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->find($this->getLocator('selectReviewerInput'))
            ->setValue($reviewer);

        $reviewersDropdownItemLocator = $this->getLocator('reviewersDropdownItem');
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $reviewersDropdownItemLocator))
            ->findAll($reviewersDropdownItemLocator)
            ->getByCriterion(new ElementTextCriterion($reviewer))
            ->click();
    }

    public function verifyReviewerIsNotSelectable(string $reviewer): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->find($this->getLocator('selectReviewerInput'))
            ->setValue($reviewer);

        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(
                new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('reviewersDropdownItem'))
            );

        $reviewersDropdownItemLocator = $this->getLocator('reviewersDropdownItem');
        $this->getHTMLPage()->findAll($reviewersDropdownItemLocator)
            ->getByCriterion(new ElementTextCriterion($reviewer))
            ->assert()
            ->hasClass('ibexa-workflow-apply-transition__user-list-item--disabled');
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('transitionPopupSelector'))->assert()->isVisible();
        $this->getHTMLPage()->waitUntilCondition(
            new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('hiddenTransitionPopupSelector'))
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('message_textarea', '.ibexa-extra-actions--workflow-apply-transition:not(.ibexa-extra-actions--hidden) textarea'),
            new VisibleCSSLocator('submit_button', '.ibexa-extra-actions--workflow-apply-transition:not(.ibexa-extra-actions--hidden) .ibexa-btn--workflow-apply'),
            new VisibleCSSLocator('selectReviewerInput', 'input.ibexa-workflow-apply-transition__user-input'),
            new VisibleCSSLocator('reviewersDropdownItem', '.ibexa-workflow-apply-transition__user-list-item'),
            new VisibleCSSLocator('transitionPopupSelector', '.ibexa-extra-actions--workflow-apply-transition'),
            new VisibleCSSLocator('hiddenTransitionPopupSelector', '.ibexa-extra-actions--workflow-apply-transition.ibexa-extra-actions--hidden'),
        ];
    }
}
