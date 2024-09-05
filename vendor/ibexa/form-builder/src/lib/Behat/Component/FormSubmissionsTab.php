<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class FormSubmissionsTab extends Component
{
    public function view(string $submissionName): void
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('submissionRow'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('submissionName'), $submissionName))
            ->find($this->getLocator('viewSubmission'))
            ->click();
    }

    public function delete(string $submissionName): void
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('submissionRow'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('submissionName'), $submissionName))
            ->find($this->getLocator('select'))
            ->click();

        $this->getHTMLPage()->find($this->getLocator('deleteSubmissions'))->click();
    }

    public function isDeleteButtonEnabled(): bool
    {
        return $this->getHTMLPage()
                ->find($this->getLocator('deleteSubmissions'))
                ->hasAttribute('disabled') === false;
    }

    public function getSubmissionsCount(): int
    {
        return $this->getHTMLPage()->findAll($this->getLocator('submissionRow'))->count();
    }

    public function hasSubmission(string $submissionName): bool
    {
        return $this->getHTMLPage()
            ->findAll($this->getLocator('submissionRow'))
            ->filterBy(new ChildElementTextCriterion($this->getLocator('submissionName'), $submissionName))
            ->any();
    }

    public function verifyIsLoaded(): void
    {
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('submissionRow', '#ibexa-tab-location-view-submissions tbody tr'),
            new VisibleCSSLocator('submissionName', 'td:nth-of-type(2)'),
            new VisibleCSSLocator('viewSubmission', '.ibexa-btn--content-submission-view'),
            new VisibleCSSLocator('deleteSubmissions', 'button#delete-submissions-submission_remove_remove'),
            new VisibleCSSLocator('select', ' input'),
        ];
    }
}
