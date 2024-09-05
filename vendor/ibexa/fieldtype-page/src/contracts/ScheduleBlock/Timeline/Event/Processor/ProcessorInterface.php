<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\Processor;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface;

interface ProcessorInterface
{
    /**
     * @param \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface $event
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     */
    public function process(EventInterface $event, BlockValue $blockValue): void;

    /**
     * @return string
     */
    public function getSupportedEventType(): string;
}

class_alias(ProcessorInterface::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\Processor\ProcessorInterface');
