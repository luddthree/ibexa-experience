<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

use Ibexa\Behat\Browser\Element\Condition\ElementHasTextCondition;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class TargetingPreview extends BlockPreview
{
    protected string $expectedHeader;

    /**
     * @param array<string, string> $data
     */
    public function setExpectedData(array $data): void
    {
        $this->expectedHeader = $data['parameter'];
    }

    public function verifyPreview(): void
    {
        $this->getHTMLPage()
            ->setTimeout(5)->waitUntilCondition(new ElementHasTextCondition($this->getHTMLPage(), $this->getLocator('targetingHeader'), $this->expectedHeader))
            ->find($this->getLocator('targetingHeader'))->assert()->textEquals($this->expectedHeader);
    }

    public function getSupportedBlockType(): string
    {
        return 'Targeting';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('targetingHeader', '.block-targeting h3'),
        ];
    }
}
