<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event\Processor;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface;
use Ibexa\FieldTypePage\Registry\ScheduleBlockEventProcessorRegistry;

class EventProcessorDispatcher
{
    /** @var \Ibexa\FieldTypePage\Registry\ScheduleBlockEventProcessorRegistry */
    private $registry;

    /**
     * @param \Ibexa\FieldTypePage\Registry\ScheduleBlockEventProcessorRegistry $registry
     */
    public function __construct(
        ScheduleBlockEventProcessorRegistry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface $event
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     */
    public function dispatch(
        EventInterface $event,
        BlockValue $blockValue
    ): void {
        $processor = $this->registry->getProcessor($event->getType());
        $processor->process($event, $blockValue);
    }
}

class_alias(EventProcessorDispatcher::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\Processor\EventProcessorDispatcher');
