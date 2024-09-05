<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event\Processor;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface;
use Ibexa\FieldTypePage\ScheduleBlock\ScheduleBlock;

class ItemAddedEventProcessor extends AbstractEventProcessor
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedEventType(): string
    {
        return 'itemAdded';
    }

    /**
     * {@inheritdoc}
     *
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event\ItemAddedEvent|\Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface $event
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     */
    public function process(EventInterface $event, BlockValue $blockValue): void
    {
        $this->getScheduleService()->addItem($blockValue, $event->getItem());
        $items = $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS)->getValue();
        $this->getScheduleService()->arrangeItems($blockValue, $items);
    }
}

class_alias(ItemAddedEventProcessor::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\Processor\ItemAddedEventProcessor');
