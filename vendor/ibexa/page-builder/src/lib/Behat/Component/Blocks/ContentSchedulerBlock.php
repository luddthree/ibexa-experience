<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\Blocks;

use Behat\Mink\Session;
use DateTime;
use Ibexa\AdminUi\Behat\Component\DateAndTimePopup;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementTransitionHasEndedCondition;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class ContentSchedulerBlock extends CollectionBlock
{
    public function __construct(
        Session $session,
        DateAndTimePopup $dateAndTimePopup,
        UniversalDiscoveryWidget $universalDiscoveryWidget,
        IbexaDropdown $ibexaDropdown
    ) {
        parent::__construct($session, $dateAndTimePopup, $universalDiscoveryWidget, $ibexaDropdown);
        $this->locators->replace(new VisibleCSSLocator('placeholderSelector', '.ibexa-pb-schedule-placeholder'));
        $this->locators->replace(new VisibleCSSLocator('itemName', '.ibexa-pb-schedule-active-item__label'));
        $this->locators->replace(new VisibleCSSLocator('item', '.ibexa-pb-schedule-active-item'));
        $this->locators->replace(new VisibleCSSLocator('nthItem', 'li.ibexa-pb-schedule-active-item:nth-of-type(%d)'));
    }

    public function setDefaultTestingConfiguration(string $blockName): void
    {
        $this->setInputField('Name', $blockName);
        $this->addContentItems([
            'Media',
            'Media/Files',
        ]);
        $this->submitForm();
    }

    public function addContentItems(array $paths): void
    {
        $expectedContentNames = array_reverse(array_map(static function (string $path) {
            return explode('/', $path)[substr_count($path, '/')];
        }, $paths));

        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()
            ->setTimeout(10)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('embedButton')));
        $this->getHTMLPage()->find($this->getLocator('embedButton'))->click();
        $this->switchBackToEditor();

        $this->universalDiscoveryWidget->verifyIsLoaded();
        foreach ($paths as $path) {
            $this->universalDiscoveryWidget->selectContent($path);
        }
        $this->universalDiscoveryWidget->confirm();

        $this->getHTMLPage()
             ->setTimeout(3)
             ->waitUntilCondition(
                 new ElementTransitionHasEndedCondition($this->getHTMLPage(), $this->getLocator('airtimePopup'))
             );
        $this->getHTMLPage()->setTimeout(3)->find($this->getLocator('airtimePopupTitle'))->assert()->textEquals('Content airtime settings');
        $this->dateAndTimePopup->setParentLocator($this->getLocator('airtimePopupContent'));
        $this->dateAndTimePopup->verifyIsLoaded();

        $this->getHTMLPage()->find($this->getLocator('airtimeSubmitButton'))->click();

        $this->getHTMLPage()
            ->waitUntilCondition(new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('airtimeBackgroundFade')));

        $this->switchIntoSettingsIframe();

        $actualContentNames = $this->getDisplayedItems();

        Assert::assertEquals($expectedContentNames, $actualContentNames);
        $this->switchBackToEditor();
    }

    public function changeAirtime(string $itemName, DateTime $newAirtime): void
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()->findAll($this->getLocator('item'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('itemName'), $itemName))
            ->find($this->getLocator('airtimeSelect'))
            ->click();

        $this->switchBackToEditor();

        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(
                new ElementTransitionHasEndedCondition($this->getHTMLPage(), $this->getLocator('airtimePopup'))
            );
        $this->getHTMLPage()->setTimeout(3)->find($this->getLocator('airtimePopupTitle'))->assert()->textEquals('Content airtime settings');
        $this->getHTMLPage()->setTimeout(3)->find($this->getLocator('airtimeBackgroundFade'))->assert()->isVisible();

        $this->dateAndTimePopup->setParentLocator($this->getLocator('airtimePopupContent'));
        $this->dateAndTimePopup->verifyIsLoaded();
        $this->dateAndTimePopup->setDate($newAirtime, 'F j, Y');

        $this->getHTMLPage()->find($this->getLocator('airtimeSubmitButton'))->click();

        $this->getHTMLPage()
            ->waitUntilCondition(new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('airtimeBackgroundFade')));

        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('queueItem')));
        $this->switchBackToEditor();
    }

    public function getDefaultPreviewData(): array
    {
        return ['parameter1' => 'Files,Media'];
    }

    public function specifyLocators(): array
    {
        return array_merge(
            parent::specifyLocators(),
            [
                new VisibleCSSLocator('airtimeSelect', '.ibexa-btn--change-airtime'),
                new VisibleCSSLocator('airtimeSubmitButton', '.m-page-builder__airtime-container .ibexa-btn--filled-info'),
                new VisibleCSSLocator('airtimePopupTitle', '.c-pb-config-popup--airtime.show .modal-title'),
                new VisibleCSSLocator('airtimePopup', '.c-pb-config-popup--airtime.show'),
                new VisibleCSSLocator('airtimePopupContent', '.c-pb-config-popup--airtime.show .modal-content'),
                new VisibleCSSLocator('airtimeBackgroundFade', '.c-pb-config-popup.modal.fade.c-pb-config-popup--airtime.show'),
                new VisibleCSSLocator('queueItem', '.ibexa-pb-schedule-queue-item'),
            ]
        );
    }

    public function getBlockType(): string
    {
        return 'Content Scheduler';
    }
}
