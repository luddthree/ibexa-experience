<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class GalleryPreview extends BlockPreview
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
            ->find($this->getLocator('galleryHeader'))
            ->assert()->textEquals($this->expectedHeader);
    }

    public function getSupportedBlockType(): string
    {
        return 'Gallery';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('galleryHeader', '.block-gallery h4'),
        ];
    }
}
