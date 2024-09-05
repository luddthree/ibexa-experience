<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class TextPreview extends BlockPreview
{
    private $expectedHeaderContent;

    private $expectedEmbedInlineLink;

    private $expectedEmbedLink;

    /** @var array */
    private $expectedUnorderedListElements;

    public function setExpectedData(array $data): void
    {
        $this->expectedHeaderContent = $data['header'];
        $this->expectedEmbedInlineLink = $data['embedInline'];
        $this->expectedEmbedLink = $data['embed'];
        $this->expectedUnorderedListElements = $data['list'];
    }

    public function verifyPreview(): void
    {
        $this->verifyHeader();
        $this->verifyEmbedInline();
        $this->verifyEmbed();
        $this->verifyUnorderedList();
    }

    public function getSupportedBlockType(): string
    {
        return 'Text';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('headerSelector', '.block-richtext h1 a'),
            new VisibleCSSLocator('embedInlineSelector', '.block-richtext h1 a'),
            new VisibleCSSLocator('embedSelector', '.block-richtext h3 a'),
            new VisibleCSSLocator('unorderedListSelector', '.block-richtext ul li'),
        ];
    }

    private function verifyUnorderedList(): void
    {
        $actualListElements = $this->getHTMLPage()
            ->findAll($this->getLocator('unorderedListSelector'))
            ->mapBy(new ElementTextMapper());

        Assert::assertEquals($this->expectedUnorderedListElements, $actualListElements);
    }

    private function verifyEmbed(): void
    {
        $actualHyperlink = $this->getHTMLPage()->find($this->getLocator('embedSelector'))->getAttribute('href');
        Assert::assertStringContainsString($this->expectedEmbedLink, $actualHyperlink);
    }

    private function verifyEmbedInline(): void
    {
        $actualHyperlink = $this->getHTMLPage()->find($this->getLocator('embedInlineSelector'))->getAttribute('href');
        Assert::assertStringContainsString($this->expectedEmbedInlineLink, $actualHyperlink);
    }

    private function verifyHeader(): void
    {
        $this->getHTMLPage()
            ->setTimeout(10)
            ->find($this->getLocator('headerSelector'))
            ->assert()->textEquals($this->expectedHeaderContent);
    }
}
