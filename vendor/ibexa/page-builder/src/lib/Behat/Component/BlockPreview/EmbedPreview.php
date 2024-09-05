<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class EmbedPreview extends BlockPreview
{
    protected $expectedHeader;

    public function setExpectedData(array $data): void
    {
        $this->expectedHeader = $data['parameter1'];
    }

    public function verifyPreview(): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->find($this->getLocator('embedHeader'))
            ->assert()->textEquals($this->expectedHeader);
    }

    public function getSupportedBlockType(): string
    {
        return 'Embed';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('embedHeader', '.block-embed h3'),
        ];
    }
}
