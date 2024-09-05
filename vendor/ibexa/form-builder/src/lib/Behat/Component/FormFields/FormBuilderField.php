<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementsCountCondition;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

abstract class FormBuilderField extends Component implements FormFieldInterface
{
    /** @var \Ibexa\AdminUi\Behat\Component\IbexaDropdown */
    protected $ibexaDropdown;

    public function __construct(Session $session, IbexaDropdown $ibexaDropdown)
    {
        parent::__construct($session);
        $this->ibexaDropdown = $ibexaDropdown;
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('configurationIframe', '.m-ibexa-fb__popup-wrapper iframe'),
            new VisibleCSSLocator('navigationTab', '.ibexa-tabs .nav-item'),
            new VisibleCSSLocator('nthNavigationTab', '.nav-item:nth-child(%d) .nav-link'),
            new VisibleCSSLocator('activeTab', '.active'),
            new VisibleCSSLocator('activeTabContent', '.ibexa-field-config__tab.active'),
            new VisibleCSSLocator('field', '.ibexa-tab-content__pane.active .form-group'),
            new VisibleCSSLocator('fieldLabel', '.ibexa-label'),
            new VisibleCSSLocator('fieldInput', 'input[type="text"],input[type="number"],textarea.ibexa-input'),
            new VisibleCSSLocator('fieldTextarea', 'textarea'),
            new VisibleCSSLocator('fieldSelect', 'div.ibexa-dropdown__wrapper > ul.ibexa-dropdown__selection-info'),
            new VisibleCSSLocator('fieldSelectValue', 'div.ibexa-dropdown__wrapper > ul.ibexa-dropdown__selection-info > li:nth-child(1)'),
            new VisibleCSSLocator('fieldCheckbox', '.ibexa-toggle__switcher'),
            new VisibleCSSLocator('submit', 'button[type=submit]'),
            new VisibleCSSLocator('discard', 'button[data-form-action=discard]'),
            new VisibleCSSLocator('droppablePlaceholder', '.droppable-placeholder'),
            new VisibleCSSLocator('configPopupTitle', '.ibexa-pb-config-panel__title'),
            new VisibleCSSLocator('addOption', '.ibexa-fb-form-field-config-options__add-btn'),
            new VisibleCSSLocator('optionInput', '.ibexa-fb-form-field-config-option__input'),
            new CSSLocator('optionInputValues', '#field_configuration_attributes_options_value'),
            new VisibleCSSLocator('nthOption', '.fb-config-option:nth-child(%d)'),
            new CSSLocator('fieldSettingsSpinner', '.c-iframe.c-iframe--is-loading'),
        ];
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(5)
            ->find($this->getLocator('configurationIframe'))
            ->assert()->isVisible();
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('fieldSettingsSpinner')));
        $this->switchBackToEditor();
    }

    protected function switchIntoSettingsIframe(): void
    {
        $settingsIframeName = $this->getHTMLPage()
                ->setTimeout(5)
                ->find($this->getLocator('configurationIframe'))
                ->assert()->isVisible()
                ->getAttribute('id');
        $this->getSession()->getDriver()->switchToIFrame($settingsIframeName);
    }

    protected function switchBackToEditor(): void
    {
        $this->getSession()->getDriver()->switchToIFrame();
    }

    protected function fillField(string $fieldName, string $fieldValue): void
    {
        $this->switchIntoSettingsIframe();
        $this->getField($fieldName)
            ->find($this->getLocator('fieldInput'))
            ->setValue($fieldValue);
        $this->switchBackToEditor();
    }

    protected function getFieldValue(string $fieldName): string
    {
        $this->switchIntoSettingsIframe();
        $value = $this->getField($fieldName)
            ->find($this->getLocator('fieldInput'))
            ->getValue();
        $this->switchBackToEditor();

        return $value;
    }

    protected function check(string $fieldName)
    {
        $this->switchIntoSettingsIframe();
        //TODO: refactor after redesign
        $element = $this->getField($fieldName)
            ->find($this->getLocator('fieldCheckbox'));
        $clickOnToggleScript = sprintf(
            'document.evaluate("%s", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.click()',
            $element->getXpath(),
        );
        $this->getSession()->executeScript($clickOnToggleScript);
        $this->switchBackToEditor();
    }

    protected function getCheckValue(string $fieldName)
    {
        $this->switchIntoSettingsIframe();
        $value = $this->getField($fieldName)
            ->find($this->getLocator('fieldCheckbox'))
            ->find(new CSSLocator('checkboxInputField', '.ibexa-toggle__input'))
            ->getValue();
        $this->switchBackToEditor();

        return $value === '1';
    }

    protected function select(string $fieldName, string $value)
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()->find($this->getLocator('fieldSelect'))->click();
        $this->ibexaDropdown->selectOption($value);
        $this->switchBackToEditor();
    }

    protected function getSelectValue(string $fieldName)
    {
        $this->switchIntoSettingsIframe();
        $value = $this->getField($fieldName)
            ->find($this->getLocator('fieldSelectValue'))
            ->getText();
        $this->switchBackToEditor();

        return $value;
    }

    protected function switchTab(string $tabName): void
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()
            ->findAll($this->getLocator('navigationTab'))
            ->getByCriterion(new ElementTextCriterion($tabName))
            ->click();
        $this->switchBackToEditor();
    }

    protected function discard()
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()->find($this->getLocator('discard'))->click();
        $this->switchBackToEditor();
    }

    public function submitForm(): void
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()->find($this->getLocator('submit'))->click();
        $this->switchBackToEditor();

        $this->getHTMLPage()->waitUntilCondition(new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('configurationIframe')));
    }

    protected function addOption(string $optionName): void
    {
        $this->switchIntoSettingsIframe();

        $optionsLocator = $this->getLocator('optionInput');
        $optionsCount = $this->getHTMLPage()->findAll($optionsLocator)->count();

        if ($this->getHTMLPage()->findAll($optionsLocator)->last()->getValue() !== '') {
            $this->getHTMLPage()->find($this->getLocator('addOption'))->click();
            $this->getHTMLPage()->waitUntilCondition(new ElementsCountCondition($this->getHTMLPage(), $optionsLocator, $optionsCount + 1));
        }

        $this->getHTMLPage()->findAll($this->getLocator('optionInput'))->last()->setValue($optionName);

        $this->switchBackToEditor();
    }

    protected function getOptions(): array
    {
        $this->switchIntoSettingsIframe();
        $optionInputsValues = $this->getHTMLPage()->find($this->getLocator('optionInputValues'))
            ->getAttribute('value');
        $options = explode(',', str_replace(str_split('[]"'), '', $optionInputsValues));
        $this->switchBackToEditor();

        return $options;
    }

    protected function getField($fieldName): ElementInterface
    {
        return $this->getHTMLPage()
            ->findAll($this->getLocator('field'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('fieldLabel'), $fieldName));
    }
}
