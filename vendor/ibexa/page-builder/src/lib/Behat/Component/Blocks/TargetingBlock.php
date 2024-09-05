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
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class TargetingBlock extends PageBuilderBlock
{
    public function __construct(
        Session $session,
        DateAndTimePopup $dateAndTimePopup,
        UniversalDiscoveryWidget $universalDiscoveryWidget,
        IbexaDropdown $ibexaDropdown
    ) {
        parent::__construct($session, $dateAndTimePopup, $universalDiscoveryWidget, $ibexaDropdown);
        $this->locators->replace(new VisibleCSSLocator('embedButton', 'div.ibexa-pb-block-embed-field > button'));
    }

    public function setDefaultContentItem(string $defaultContentItem): void
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()
            ->setTimeout(10)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('embedButton')));
        $this->getHTMLPage()->find($this->getLocator('embedButton'))->click();
        $this->switchBackToEditor();

        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($defaultContentItem);
        $this->universalDiscoveryWidget->confirm();

        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()
            ->waitUntilCondition(
                new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('defaultContentItemName'))
            );

        $partsOfContentItemPath = explode('/', $defaultContentItem);
        $defaultContentItemLastValue = end($partsOfContentItemPath);

        $this->getHTMLPage()->find($this->getLocator('defaultContentItemName'))->assert()->textEquals($defaultContentItemLastValue);
        $this->switchBackToEditor();
    }

    public function addContentItemInSegmentSetup(string $contentItemInSegmentSetup): void
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()
            ->setTimeout(10)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('addContentToSegmentButton')));
        $this->getHTMLPage()->find($this->getLocator('addContentToSegmentButton'))->click();
        $this->switchBackToEditor();

        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($contentItemInSegmentSetup);
        $this->universalDiscoveryWidget->confirm();

        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()->setTimeout(10)
            ->waitUntilCondition(
                new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('ContentAddedToSegmentName'))
            );

        $partsOfContentItemPath = explode('/', $contentItemInSegmentSetup);
        $lastValueOfContentItemPath = end($partsOfContentItemPath);

        $this->getHTMLPage()->find($this->getLocator('ContentAddedToSegmentName'))->assert()->textEquals($lastValueOfContentItemPath);
        $this->switchBackToEditor();
    }

    public function selectSegment(string $selectedSegment): void
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('selectSegmentDropdown'))->click();
        $this->ibexaDropdown->verifyIsLoaded();
        $this->ibexaDropdown->selectOption($selectedSegment);
        $this->switchBackToEditor();
    }

    public function getBlockType(): string
    {
        return 'Targeting';
    }

    public function specifyLocators(): array
    {
        return array_merge(
            parent::specifyLocators(),
            [
                //default config
                new VisibleCSSLocator('defaultContentItemName', 'div.ibexa-pb-embed-meta__title'),
                //segment table
                new VisibleCSSLocator('addContentToSegmentButton', 'li.ibexa-segmentation__item:last-of-type div.ibexa-segmentation__content-wrapper > button'),
                new VisibleCSSLocator('ContentAddedToSegmentName', 'li:last-of-type .ibexa-segmentation__content-wrapper .ibexa-tag__content span .ibexa-middle-ellipsis__name--start'),
                new VisibleCSSLocator('selectSegmentDropdown', 'li:last-of-type .ibexa-segmentation__select-wrapper .ibexa-dropdown__wrapper'),
            ]
        );
    }

    public function setDefaultTestingConfiguration(string $blockName): void
    {
        $this->setInputField('Name', $blockName);
        $this->setDefaultContentItem('Ibexa Digital Experience Platform/Ibexa Platform');
        $this->submitForm();
    }

    /**
     * @return array<string, mixed> $previewData
     */
    public function getDefaultPreviewData(): array
    {
        $previewData = ['parameter' => 'Ibexa Platform'];

        return $previewData;
    }
}
