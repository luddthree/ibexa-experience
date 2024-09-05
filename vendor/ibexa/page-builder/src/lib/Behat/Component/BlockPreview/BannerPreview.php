<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

use Ibexa\Behat\Browser\Locator\CSSLocator;
use PHPUnit\Framework\Assert;

class BannerPreview extends BlockPreview
{
    protected $expectedLink;

    public function setExpectedData(array $data): void
    {
        $this->expectedLink = $data['parameter1'];
    }

    public function verifyPreview(): void
    {
        Assert::assertEquals($this->expectedLink, $this->getHTMLPage()->setTimeout(3)->find($this->getLocator('link'))->getAttribute('href'));
    }

    public function getSupportedBlockType(): string
    {
        return 'Banner';
    }

    protected function specifyLocators(): array
    {
        return [
            new CSSLocator('link', '.block-banner a'),
        ];
    }
}
