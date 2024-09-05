<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event\Processor;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface;
use Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\Processor\ProcessorInterface;
use Ibexa\FieldTypePage\ScheduleBlock\ScheduleService;

abstract class AbstractEventProcessor implements ProcessorInterface
{
    /** @var \Ibexa\FieldTypePage\ScheduleBlock\ScheduleService */
    private $scheduleService;

    /**
     * @param \Ibexa\FieldTypePage\ScheduleBlock\ScheduleService $scheduleService
     */
    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * @return \Ibexa\FieldTypePage\ScheduleBlock\ScheduleService
     */
    public function getScheduleService(): ScheduleService
    {
        return $this->scheduleService;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getSupportedEventType(): string;

    /**
     * {@inheritdoc}
     */
    abstract public function process(EventInterface $event, BlockValue $blockValue): void;
}

class_alias(AbstractEventProcessor::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\Processor\AbstractEventProcessor');
