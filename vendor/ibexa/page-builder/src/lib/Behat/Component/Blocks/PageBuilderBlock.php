<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\Blocks;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\DateAndTimePopup;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

abstract class PageBuilderBlock extends Component implements PageBuilderBlockInterface
{
    /** @var \Ibexa\AdminUi\Behat\Component\DateAndTimePopup */
    protected $dateAndTimePopup;

    /** @var \Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget */
    protected $universalDiscoveryWidget;

    /** @var \Ibexa\AdminUi\Behat\Component\IbexaDropdown */
    protected $ibexaDropdown;

    public function __construct(
        Session $session,
        DateAndTimePopup $dateAndTimePopup,
        UniversalDiscoveryWidget $universalDiscoveryWidget,
        IbexaDropdown $ibexaDropdown
    ) {
        parent::__construct($session);
        $this->dateAndTimePopup = $dateAndTimePopup;
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
        $this->ibexaDropdown = $ibexaDropdown;
    }

    abstract public function setDefaultTestingConfiguration(string $blockName);

    abstract public function getDefaultPreviewData(): array;

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('configWindow'))->assert()->isVisible();
    }

    public function setInputField(string $fieldLabel, string $value): void
    {
        $this->switchIntoSettingsIframe();

        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('blockConfigField')));
        $this->getHTMLPage()
            ->findAll($this->getLocator('blockConfigField'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('blockConfigFieldLabel'), $fieldLabel))
            ->find($this->getLocator('blockConfigFieldInput'))
            ->setValue($value)
        ;

        $this->switchBackToEditor();
    }

    public function switchTab(string $tabName): void
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()->findAll($this->getLocator('blockTab'))->getByCriterion(new ElementTextCriterion($tabName))->click();
        $this->switchBackToEditor();
    }

    public function setLayout(string $layoutName)
    {
        $this->switchTab('Design');
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()->find(new VisibleCSSLocator('layoutDropdown', '.ibexa-dropdown.ibexa-dropdown--single'))->click();
        $this->ibexaDropdown->verifyIsLoaded();
        $this->ibexaDropdown->selectOption($layoutName);
        $this->switchBackToEditor();
        $this->switchTab('Properties');
    }

    public function setRevealDate(\DateTime $date)
    {
        $this->switchTab('Scheduler');

        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()->find($this->getLocator('revealLater'))->click();

        $this->getHTMLPage()->find($this->getLocator('revealLaterDateInput'))->click();

        $this->dateAndTimePopup->setParentLocator($this->getLocator('revealLaterContainer'));
        $this->dateAndTimePopup->setDate($date);
        $this->switchBackToEditor();
        // Close the date picker by clicking outside of it
        $this->switchTab('Scheduler');
    }

    public function setHideDate(\DateTime $date)
    {
        $this->switchTab('Scheduler');

        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()->find($this->getLocator('tillUpTo'))->click();

        $this->getHTMLPage()->find($this->getLocator('tillUpDateInput'))->click();

        $this->dateAndTimePopup->setParentLocator($this->getLocator('tillUpToContainer'));
        $this->dateAndTimePopup->setDate($date);
        $this->switchBackToEditor();
        // Close the date picker by clicking outside of it
        $this->switchTab('Scheduler');
    }

    public function submitForm(): void
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()
            ->findAll($this->getLocator('blockConfigurationButton'))
            ->getByCriterion(new ElementTextCriterion('Save and close'))
            ->click();
        $this->switchBackToEditor();

        $this->getHTMLPage()->setTimeout(3)
            ->waitUntilCondition(
                new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('configWindow'))
            )
            ->waitUntilCondition(
                new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('backgroundFade'))
            );
    }

    public function submitFormWithNameLengthAssertion(): void
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()
            ->findAll($this->getLocator('blockConfigurationButton'))
            ->getByCriterion(new ElementTextCriterion('Save and close'))
            ->click();

        $this->getHTMLPage()
            ->setTimeout(15)
            ->find($this->getLocator('nameFieldLengthErrorNotification'))
            ->assert()
            ->textEquals('This value is too long. It should have 255 characters or less.');
    }

    public function cancelForm(): void
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()
            ->findAll($this->getLocator('blockConfigurationButton'))
            ->getByCriterion(new ElementTextCriterion('Discard'))
            ->click();
        $this->switchBackToEditor();

        $this->getHTMLPage()
            ->setTimeout(10)
            ->waitUntilCondition(
                new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('configWindow'))
            )
            ->waitUntilCondition(
                new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('backgroundFade'))
            );
    }

    public function addContent(string $path): void
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()->find($this->getLocator('embedButton'))->click();
        $this->switchBackToEditor();

        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($path);
        $this->universalDiscoveryWidget->confirm();

        $expectedItemName = explode('/', $path)[substr_count($path, '/')];

        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()->find($this->getLocator('embeddedItemTitle'))->assert()->textEquals($expectedItemName);
        $this->switchBackToEditor();
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('iframeSelector', '.c-pb-iframe__preview:not(#page-builder-preview)'),
            new VisibleCSSLocator('configWindow', '.ibexa-pb-config-panel'),
            new VisibleCSSLocator('backgroundFade', '.modal-backdrop.fade.show'),
            new VisibleCSSLocator('blockConfigurationButton', '.ibexa-pb-block-config__actions button'),
            new VisibleCSSLocator('blockPreviewSelector', 'div[data-type=preview]'),
            new VisibleCSSLocator('blockConfigField', '.ibexa-pb-block-config__field'),
            new VisibleCSSLocator('blockConfigFieldLabel', '.ibexa-label'),
            new VisibleCSSLocator('blockConfigFieldInput', '.ibexa-data-source__input'),
            new VisibleCSSLocator('embeddedItemTitle', '.ibexa-pb-block-config__fields .ibexa-pb-embed-meta__title'),
            new VisibleCSSLocator('blockNameSelector', '.c-pb-action-menu__name'),
            new VisibleCSSLocator('blockTab', '.ibexa-pb-block-config__content > .ibexa-header .nav-link'),
            new VisibleCSSLocator('revealLater', '#block_configuration_since_type_1'),
            new VisibleCSSLocator('revealLaterDateInput', '#block_configuration_since .ibexa-picker__input'),
            new VisibleCSSLocator('revealLaterContainer', '.ibexa-pb-reveal-hide-date-time__reveal'),
            new VisibleCSSLocator('tillUpTo', '#block_configuration_till_type_1'),
            new VisibleCSSLocator('tillUpDateInput', '#block_configuration_till .ibexa-picker__input'),
            new VisibleCSSLocator('tillUpToContainer', '.ibexa-pb-reveal-hide-date-time__hide'),
            new VisibleCSSLocator('layoutSelect', '#block_configuration_view'),
            new VisibleCSSLocator('blockSpinner', '.c-pb-iframe--is-loading .c-pb-iframe__loading'),
            new VisibleCSSLocator('embedButton', '.ibexa-pb-block-config__fields .ibexa-pb-block-embed-field button'),
            new VisibleCSSLocator('nameFieldLengthErrorNotification', '.ibexa-pb-block-config__field--name .ibexa-pb-block-config__error'),
        ];
    }

    protected function switchIntoSettingsIframe(): void
    {
        $settingsIframeName = $this->getHTMLPage()
            ->setTimeout(10)
            ->find($this->getLocator('iframeSelector'))
            ->assert()->isVisible()
            ->getAttribute('id');
        $this->getSession()->getDriver()->switchToIFrame($settingsIframeName);
    }

    protected function switchBackToEditor(): void
    {
        $this->getSession()->getDriver()->switchToIFrame();
    }
}
