<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event\Processor;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface;
use Ibexa\FieldTypePage\ScheduleBlock\Item\Item;
use Ibexa\FieldTypePage\ScheduleBlock\ScheduleBlock;

class ItemsReorderedEventProcessor extends AbstractEventProcessor
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedEventType(): string
    {
        return 'itemsReordered';
    }

    /**
     * {@inheritdoc}
     *
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event\ItemsReorderedEvent|\Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface $event
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     */
    public function process(EventInterface $event, BlockValue $blockValue): void
    {
        $items = $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS)->getValue();
        $itemIds = array_map(static function (Item $item) { return $item->getId(); }, $items);

        $items = array_combine($itemIds, $items);

        $map = $event->getMap();
        $reorderedItems = array_fill_keys($map, null);
        $items = array_replace($reorderedItems, $items);

        $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS)->setValue($items);
        $this->getScheduleService()->arrangeItems($blockValue, $items);
    }
}

class_alias(ItemsReorderedEventProcessor::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\Processor\ItemsReorderedEventProcessor');
