<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class RSSPreview extends BlockPreview
{
    protected $expectedItemCount;

    public function setExpectedData(array $data): void
    {
        $this->expectedItemCount = (int)$data['parameter1'];
    }

    public function verifyPreview(): void
    {
        $this->getHTMLPage()
            ->setTimeout(10)
            ->findAll($this->getLocator('item'))
            ->assert()->countEquals($this->expectedItemCount);
    }

    public function getSupportedBlockType(): string
    {
        return 'RSS';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('item', '.block-rss .block-rss-items li'),
        ];
    }
}
