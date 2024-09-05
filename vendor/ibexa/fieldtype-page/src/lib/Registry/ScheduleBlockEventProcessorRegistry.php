<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Registry;

use Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\Processor\ProcessorInterface;

class ScheduleBlockEventProcessorRegistry
{
    /** @var \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\Processor\ProcessorInterface[] */
    private $processors;

    /**
     * @param iterable|\Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\Processor\ProcessorInterface[] $processors
     */
    public function __construct(iterable $processors = [])
    {
        foreach ($processors as $processor) {
            $this->processors[$processor->getSupportedEventType()] = $processor;
        }
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\Processor\ProcessorInterface[]
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\Processor\ProcessorInterface[] $processors
     */
    public function setProcessors(array $processors): void
    {
        $this->processors = $processors;
    }

    /**
     * @param string $eventType
     *
     * @return bool
     */
    public function hasProcessor(string $eventType): bool
    {
        return isset($this->processors[$eventType]);
    }

    /**
     * @param string $eventType
     *
     * @return \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\Processor\ProcessorInterface
     */
    public function getProcessor(string $eventType): ProcessorInterface
    {
        return $this->processors[$eventType];
    }
}

class_alias(ScheduleBlockEventProcessorRegistry::class, 'EzSystems\EzPlatformPageFieldType\Registry\ScheduleBlockEventProcessorRegistry');
