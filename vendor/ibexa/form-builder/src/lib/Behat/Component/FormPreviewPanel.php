<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class FormPreviewPanel extends Component
{
    public function createForm(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createForm'))->click();
    }

    public function editForm(): void
    {
        $this->getHTMLPage()->find($this->getLocator('editForm'))->click();
    }

    public function deleteForm(): void
    {
        $this->getHTMLPage()->find($this->getLocator('deleteForm'))->click();
    }

    public function isFieldPresent(string $label): bool
    {
        $this->getSession()->getDriver()->switchToIFrame($this->getLocator('formPreviewName')->getSelector());
        $isPresent = $this->getHTMLPage()
            ->findAll($this->getLocator('formField'))
            ->filterBy(new ChildElementTextCriterion($this->getLocator('formFieldLabel'), $label))
            ->any();
        $this->getSession()->getDriver()->switchToIFrame(null);

        return $isPresent;
    }

    public function hasHiddenField(): bool
    {
        $this->getSession()->getDriver()->switchToIFrame($this->getLocator('formPreviewName')->getSelector());
        $isPresent = $this->getHTMLPage()
            ->findAll($this->getLocator('hiddenField'))
            ->any();
        $this->getSession()->getDriver()->switchToIFrame(null);

        return $isPresent;
    }

    public function verifyIsEmpty()
    {
        return $this->getHTMLPage()->findAll($this->getLocator('emptyForm'))->any();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->waitUntilCondition(
            new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('formPreviewLocator'))
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('contentFieldPreview', '.ibexa-content-field'),
            new VisibleCSSLocator('formPreviewName', 'form-preview'),
            new VisibleCSSLocator('formPreviewLocator', '[name=form-preview]'),
            new VisibleCSSLocator('createForm', '.ibexa-fb-content-edit-form__btn--create'),
            new VisibleCSSLocator('emptyForm', '.ibexa-fb-content-edit-form--no-value'),
            new VisibleCSSLocator('editForm', '.ibexa-fb-content-edit-form__preview-action--edit'),
            new VisibleCSSLocator('deleteForm', '.ibexa-fb-content-edit-form__preview-action--trash'),
            new VisibleCSSLocator('formField', '.form-group'),
            new VisibleCSSLocator('formFieldLabel', '.ibexa-label,.form-check-label,[type=submit]'),
            new CSSLocator('hiddenField', '.ibexa-main-container > div > input[type="hidden"]'),
        ];
    }
}
