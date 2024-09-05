<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementsCountCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementTransitionHasEndedCondition;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class FormBuilderEditor extends Component
{
    public function verifyFormIsEmpty(): void
    {
        $this->getHTMLPage()->find($this->getLocator('emptyFormBuilderWorkspace'))->assert()->isVisible();
    }

    public function goBackToContentCreation(): void
    {
        $this->getHTMLPage()->find($this->getLocator('closeButton'))->click();
        $this->getHTMLPage()->waitUntilCondition(new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('formBuilderModal')));
    }

    public function addFieldToForm(string $fieldName): void
    {
        $currentFieldCount = $this->getHTMLPage()->findAll($this->getLocator('workspaceField'))->count();

        $this->getHTMLPage()->find($this->getLocator('sidebarSearch'))->setValue($fieldName);
        $fieldLocator = new VisibleCSSLocator(
            'sidebarField',
            'div.c-ibexa-fb-sidebar-field:not(.c-ibexa-fb-sidebar-field--hidden) .c-ibexa-fb-sidebar-field__content'
        );
        $this->getHTMLPage()->find($fieldLocator)->mouseOver();
        $this->getHTMLPage()->setTimeout(3)->waitUntilCondition(new ElementTransitionHasEndedCondition($this->getHTMLPage(), $fieldLocator));

        $fieldScript = sprintf("document.querySelector('%s')", $fieldLocator->getSelector());
        $workspaceScript = sprintf("document.querySelector('%s')", $this->getLocator('workspace')->getSelector());

        $this->getHTMLPage()->dragAndDrop($fieldScript, $workspaceScript, $workspaceScript);
        $this->getHTMLPage()->setTimeout(5)->waitUntilCondition(
            new ElementsCountCondition($this->getHTMLPage(), $this->getLocator('workspaceField'), $currentFieldCount + 1)
        );
        $this->getHTMLPage()->findAll($this->getLocator('workspaceField'))
            ->getByCriterion(new ElementTextCriterion($fieldName))
            ->assert()->isVisible();
    }

    public function removeField(string $fieldName)
    {
        $currentFieldCount = $this->getHTMLPage()->findAll($this->getLocator('workspaceField'))->count();
        $this->getHTMLPage()
            ->findAll($this->getLocator('workspaceField'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('workspaceFieldLabel'), $fieldName))
            ->find($this->getLocator('workspaceFieldTrash'))
            ->click();
        $this->getHTMLPage()->find($this->getLocator('formTitle'))->mouseOver();
        $this->getHTMLPage()->setTimeout(5)->waitUntilCondition(
            new ElementsCountCondition($this->getHTMLPage(), $this->getLocator('workspaceField'), $currentFieldCount - 1)
        );
    }

    public function openFieldSettings(string $fieldName)
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('workspaceField'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('workspaceFieldLabel'), $fieldName))
            ->find($this->getLocator('workspaceFieldSettings'))
            ->click();

        $this->getHTMLPage()->setTimeout(3)->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('blockSettingsModal')));
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->findAll($this->getLocator('sidebarField'))->assert()->hasElements();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('sidebarField', '.c-ibexa-fb-sidebar-field:not(.c-ibexa-fb-sidebar-field--hidden)'),
            new VisibleCSSLocator('workspaceField', '.c-ibexa-fb-form-field'),
            new VisibleCSSLocator('sidebarSearch', '.c-ibexa-fb-sidebar__sidebar-filter'),
            new VisibleCSSLocator('workspaceFieldLabel', '.c-ibexa-fb-form-field__label'),
            new VisibleCSSLocator('workspace', '.m-ibexa-fb-workspace__content'),
            new VisibleCSSLocator('workspaceFieldTrash', '.c-ibexa-fb-form-field__trash-wrapper button'),
            new VisibleCSSLocator('workspaceFieldSettings', '.c-ibexa-fb-form-field__setting-wrapper button'),
            new VisibleCSSLocator('formBuilderModal', '.ibexa-fb-modal--visible'),
            new VisibleCSSLocator('blockSettingsModal', '.ibexa-pb-config-panel'),
            new VisibleCSSLocator('closeButton', '.ibexa-fb-modal--visible [data-save-and-close-form-builder]'),
            new VisibleCSSLocator('formTitle', '.ibexa-fb-modal--visible .ibexa-fb-modal__title'),
            new VisibleCSSLocator('emptyFormBuilderWorkspace', '[data-empty-message="Create a form by dragging and dropping elements here"]'),
        ];
    }
}
