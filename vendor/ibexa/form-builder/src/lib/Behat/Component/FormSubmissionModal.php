<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class FormSubmissionModal extends Component
{
    public function verifyFieldValue(string $label, string $value): void
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('submissionRow'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('submissionLabel'), $label))
            ->find($this->getLocator('submissionValue'))
            ->assert()->textEquals($value);
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(3)->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('modal')));
        $this->getHTMLPage()->find($this->getLocator('modalTitle'))->assert()->textEquals('View submission');
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('modal', '.modal.show .modal-content'),
            new VisibleCSSLocator('modalTitle', '.modal.show .modal-content .modal-title'),
            new VisibleCSSLocator('submissionRow', '.modal.show .modal-content tr'),
            new VisibleCSSLocator('submissionLabel', 'td:nth-child(1)'),
            new VisibleCSSLocator('submissionValue', ' td:nth-child(2)'),
        ];
    }
}
