<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\Blocks;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\DateAndTimePopup;
use Ibexa\AdminUi\Behat\Component\Fields\RichText;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class TextBlock extends PageBuilderBlock
{
    /** @var \Ibexa\AdminUi\Behat\Component\Fields\RichText */
    private $richTextEditor;

    public function __construct(
        Session $session,
        DateAndTimePopup $dateAndTimePopup,
        UniversalDiscoveryWidget $universalDiscoveryWidget,
        IbexaDropdown $ibexaDropdown,
        RichText $richTextEditor
    ) {
        parent::__construct($session, $dateAndTimePopup, $universalDiscoveryWidget, $ibexaDropdown);
        $this->richTextEditor = $richTextEditor;
    }

    public function setDefaultTestingConfiguration(string $blockName): void
    {
        $this->richTextEditor->setParentLocator(new VisibleCSSLocator('blockContainer', '[name="block_configuration"]'));
        $this->setInputField('Name', $blockName);
        $this->addUnorderedList(['UnorderedList1']);
        $this->addTextWithStyle('Block header', 'Heading 1');
        $this->embedItemInline('Media/Images');
        $this->embedItem('Media/Files');
        $this->submitForm();
    }

    /**
     * Adds text to RichText Editor.
     *
     * @param string  $text value of text
     */
    public function addText(string $text): void
    {
        $this->switchIntoSettingsIframe();
        $this->richTextEditor->insertLine($text);
        $this->switchBackToEditor();
    }

    public function addTextWithStyle(string $text, string $style): void
    {
        $this->switchIntoSettingsIframe();
        $this->richTextEditor->insertLine($text, $style);
        $this->richTextEditor->insertNewLine();
        $this->switchBackToEditor();
    }

    public function embedItemInline(string $path): void
    {
        $this->switchIntoSettingsIframe();
        $this->richTextEditor->clickEmbedInlineButton();
        $this->switchBackToEditor();

        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($path);
        $this->universalDiscoveryWidget->confirm();

        $itemName = explode('/', $path)[substr_count($path, '/')];

        $this->switchIntoSettingsIframe();
        Assert::assertTrue($this->richTextEditor->equalsEmbedInlineItem($itemName));
        $this->switchBackToEditor();
    }

    public function embedItem(string $path): void
    {
        $this->switchIntoSettingsIframe();
        $this->richTextEditor->clickEmbedButton();
        $this->switchBackToEditor();

        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($path);
        $this->universalDiscoveryWidget->confirm();

        $itemName = explode('/', $path)[substr_count($path, '/')];
        $this->switchIntoSettingsIframe();
        Assert::assertTrue($this->richTextEditor->equalsEmbedItem($itemName));
        $this->switchBackToEditor();
    }

    public function addUnorderedList(array $listElements): void
    {
        $this->switchIntoSettingsIframe();
        $this->richTextEditor->addUnorderedList($listElements);
        $this->switchBackToEditor();
    }

    public function getDefaultPreviewData(): array
    {
        return [
            'header' => 'Images',
            'embedInline' => '/Media/Images',
            'embed' => '/Media/Files',
            'list' => ['UnorderedList1'],
            ];
    }

    public function getBlockType(): string
    {
        return 'Text';
    }
}
