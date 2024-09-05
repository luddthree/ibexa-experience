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
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementsCountCondition;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class CollectionBlock extends PageBuilderBlock
{
    public function __construct(
        Session $session,
        DateAndTimePopup $dateAndTimePopup,
        UniversalDiscoveryWidget $universalDiscoveryWidget,
        IbexaDropdown $ibexaDropdown
    ) {
        parent::__construct($session, $dateAndTimePopup, $universalDiscoveryWidget, $ibexaDropdown);
        $this->locators->replace(new VisibleCSSLocator('embedButton', '.ibexa-pb-block-config__fields .ibexa-btn--select-content'));
    }

    public function setDefaultTestingConfiguration(string $blockName): void
    {
        $this->setInputField('Name', $blockName);
        $this->addContentItems([
            'Ibexa Digital Experience Platform',
            'Ibexa Digital Experience Platform/Ibexa Platform',
            'Media/Images',
        ]);
        $this->submitForm();
    }

    public function addContentItems(array $paths): void
    {
        $expectedContentNames = array_map(static function (string $path) {
            return explode('/', $path)[substr_count($path, '/')];
        }, $paths);

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

        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()
            ->waitUntilCondition(
                new ElementsCountCondition($this->getHTMLPage(), $this->getLocator('itemName'), count($paths))
            );
        Assert::assertEquals($expectedContentNames, $this->getDisplayedItems());
        $this->switchBackToEditor();
    }

    public function deleteItem(string $itemName): void
    {
        $this->switchIntoSettingsIframe();

        $beforeItems = $this->getDisplayedItems();
        $this->getHTMLPage()
            ->findAll($this->getLocator('item'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('itemName'), $itemName))
            ->find($this->getLocator('trashButton'))
            ->click();

        $this->getHTMLPage()->setTimeout(5)->waitUntilCondition(new ElementsCountCondition($this->getHTMLPage(), $this->getLocator('item'), count($beforeItems) - 1));
        $this->switchBackToEditor();
    }

    public function moveItemToAnother(string $itemNameFrom, string $itemNameTo): void
    {
        $this->switchIntoSettingsIframe();
        $beforeItems = $this->getDisplayedItems();
        $this->switchBackToEditor();

        $itemPositions = array_flip(array_filter($beforeItems, static function (string $text) use ($itemNameFrom, $itemNameTo) {
            return $text === $itemNameFrom || $text === $itemNameTo;
        }));

        $fromItemPosition = $itemPositions[$itemNameFrom];
        $toItemPosition = $itemPositions[$itemNameTo];

        // Item positions are 0-indexed, but CSS uses 1-indexing
        $startItemLocator = new VisibleCSSLocator(
            'startItem',
            sprintf($this->getLocator('nthItem')->getSelector(), $fromItemPosition + 1)
        );
        $endItemLocator = new VisibleCSSLocator(
            'startItem',
            sprintf($this->getLocator('nthItem')->getSelector(), $toItemPosition + 1)
        );

        $settingIframeName = $this->getHTMLPage()
            ->setTimeout(5)
            ->find($this->getLocator('iframeSelector'))
            ->assert()->isVisible()
            ->getAttribute('id');

        $fromExpression = sprintf("document.querySelector('%s').contentDocument.querySelector('%s')", '#' . $settingIframeName, $startItemLocator->getSelector());
        $hoverExpression = sprintf("document.querySelector('%s').contentDocument.querySelector('%s')", '#' . $settingIframeName, $endItemLocator->getSelector());
        $placeholderExpression = sprintf(
            "document.querySelector('#%s').contentDocument.querySelector('%s')",
            $settingIframeName,
            $this->getLocator('placeholderSelector')->getSelector()
        );

        $this->getHTMLPage()->dragAndDrop($fromExpression, $hoverExpression, $placeholderExpression);
        $this->getHTMLPage()
            ->waitUntilCondition(
                new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('placeholderSelector'))
            );

        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(
                new ElementsCountCondition($this->getHTMLPage(), $this->getLocator('itemName'), count($beforeItems))
            );
        $afterItems = $this->getDisplayedItems();
        $this->switchBackToEditor();

        Assert::assertEquals($beforeItems[$fromItemPosition], $afterItems[$toItemPosition]);
    }

    public function getDefaultPreviewData(): array
    {
        return ['parameter1' => 'Ibexa Digital Experience Platform,Ibexa Platform,Images'];
    }

    public function getBlockType(): string
    {
        return 'Collection';
    }

    public function specifyLocators(): array
    {
        return array_merge(
            parent::specifyLocators(),
            [
                new VisibleCSSLocator('previewSelector', 'h3'),
                new VisibleCSSLocator('itemName', '.ibexa-pb-collection-item__label-name'),
                new VisibleCSSLocator('nthItem', 'li.ibexa-pb-collection-item:nth-of-type(%d)'),
                new VisibleCSSLocator('trashButton', '.ibexa-btn--trash'),
                new VisibleCSSLocator('placeholderSelector', '.ibexa-pb-collection-placeholder'),
                new VisibleCSSLocator('item', '.ibexa-pb-collection-item'),
            ]
        );
    }

    protected function getDisplayedItems(): array
    {
        return $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementNotExistsCondition(
                $this->getHTMLPage(),
                $this->getLocator('placeholderSelector')
            ))
            ->findAll($this->getLocator('itemName'))
            ->assert()->hasElements()
            ->mapBy(new ElementTextMapper());
    }
}
