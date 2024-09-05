<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class CollectionPreview extends BlockPreview
{
    protected $expectedItems;

    public function setExpectedData(array $data): void
    {
        $this->expectedItems = explode(',', $data['parameter1']);
    }

    public function getSupportedBlockType(): string
    {
        return 'Collection';
    }

    public function verifyPreview(): void
    {
        $displayedItems = $this->getHTMLPage()
            ->setTimeout(3)
            ->findAll($this->getLocator('item'))
            ->assert()->countEquals(count($this->expectedItems))
            ->mapBy(new ElementTextMapper())
        ;

        Assert::assertEquals($this->expectedItems, $displayedItems);
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('item', '.block-collection h3'),
        ];
    }
}
