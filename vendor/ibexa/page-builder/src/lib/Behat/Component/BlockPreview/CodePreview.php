<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class CodePreview extends BlockPreview
{
    protected $expectedCodeContent;

    public function setExpectedData(array $data): void
    {
        $this->expectedCodeContent = $data['parameter1'];
    }

    public function verifyPreview(): void
    {
        $trimmedCodeContent = str_replace(['\n', ' '], '', $this->getHTMLPage()->setTimeout(3)->find($this->getLocator('codeBlock'))->getOuterHtml());
        Assert::assertStringContainsString($this->expectedCodeContent, $trimmedCodeContent);
    }

    public function getSupportedBlockType(): string
    {
        return 'Code';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('codeBlock', '.block-tag'),
        ];
    }
}
