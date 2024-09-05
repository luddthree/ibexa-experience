<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\Blocks;

use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class ContentListBlock extends PageBuilderBlock
{
    public function setDefaultTestingConfiguration(string $blockName): void
    {
        $this->setInputField('Name', $blockName);
        $this->addContent('Media');
        $this->setInputField('Limit', '5');
        $this->selectContentType('Folder');
        $this->submitForm();
    }

    public function getDefaultPreviewData(): array
    {
        return ['parameter1' => 'Media', 'parameter2' => 'Images,Files,Multimedia'];
    }

    public function specifyLocators(): array
    {
        return array_merge(
            parent::specifyLocators(),
            [
                new VisibleCSSLocator('dropdownSelector', '.ibexa-dropdown__selection-info'),
            ]
        );
    }

    public function getBlockType(): string
    {
        return 'Content List';
    }

    protected function selectContentType(string $contentType)
    {
        $this->switchIntoSettingsIframe();
        $this->getHTMLPage()->find($this->getLocator('dropdownSelector'))->click();
        $this->ibexaDropdown->selectOption($contentType);
        $this->switchBackToEditor();
    }
}
