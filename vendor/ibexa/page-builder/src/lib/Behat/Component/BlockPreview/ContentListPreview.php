<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class ContentListPreview extends BlockPreview
{
    protected $expectedHeader;

    protected $expectedItems;

    public function setExpectedData(array $data): void
    {
        $this->expectedHeader = $data['parameter1'];
        $this->expectedItems = explode(',', $data['parameter2']);
    }

    public function verifyPreview(): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->find($this->getLocator('header'))
            ->assert()->textEquals($this->expectedHeader);
        $displayedItems = $this->getHTMLPage()
            ->setTimeout(3)
            ->findAll($this->getLocator('item'))
            ->assert()->containsElementsWithText($this->expectedItems);
    }

    public function getSupportedBlockType(): string
    {
        return 'Content List';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('header', '.block-contentlist h3'),
            new VisibleCSSLocator('item', '.block-contentlist .block-contentlist-child'),
        ];
    }
}
