<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

use Ibexa\Behat\Browser\Element\Condition\ElementsCountCondition;
use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class ContentSchedulerPreview extends BlockPreview
{
    protected $expectedItems;

    public function setExpectedData(array $data): void
    {
        $this->expectedItems = explode(',', $data['parameter1']);
    }

    public function verifyPreview(): void
    {
        $itemLocator = $this->getLocator('item');

        $this->getHTMLPage()->setTimeout(3)->waitUntilCondition(
            new ElementsCountCondition($this->getHTMLPage(), $itemLocator, count($this->expectedItems))
        );

        $displayedItems = $this->getHTMLPage()
            ->findAll($itemLocator)
            ->mapBy(new ElementTextMapper());

        Assert::assertEquals($this->expectedItems, $displayedItems);
    }

    public function getSupportedBlockType(): string
    {
        return 'Content Scheduler';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('item', '.block-schedule h3'),
        ];
    }
}
