<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Block\ScheduleBlock;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\ScheduleBlock\Item\Item;
use Ibexa\FieldTypePage\ScheduleBlock\ScheduleBlock;

class ConfigurationDataGenerator
{
    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     *
     * @return array
     */
    public function generate(BlockValue $blockValue): array
    {
        $slots = $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_SLOTS)->getValue();

        $activeItems = [];
        /** @var \Ibexa\FieldTypePage\ScheduleBlock\Slot $slot */
        foreach ($slots as $slot) {
            if ($slot->getItem() !== null) {
                $activeItems[] = $slot->getItem();
            }
        }

        return [
            'blockValue' => $blockValue,
            'lists' => [
                'active' => $activeItems,
                'queue' => $this->buildQueue($blockValue),
            ],
        ];
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     *
     * @return \Ibexa\FieldTypePage\ScheduleBlock\Item\Item[]
     *
     * @throws \Exception
     */
    private function buildQueue(BlockValue $blockValue): array
    {
        $itemsQueue = [];
        /** @var \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface $event */
        foreach ($blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_EVENTS)->getValue() as $event) {
            if ($event->getType() === 'itemAdded') {
                /** @var \Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event\ItemAddedEvent $event */
                $itemsQueue[] = $event->getItem();
            }

            if ($event->getType() === 'itemRemoved') {
                /** @var \Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event\ItemRemovedEvent $event */
                $itemsQueue = array_filter($itemsQueue, static function (Item $item) use ($event) {
                    return $item->getId() !== $event->getItemId();
                });
            }
        }

        $now = new \DateTimeImmutable();
        $itemsQueue = array_filter($itemsQueue, static function (Item $item) use ($now) {
            return $item->getAdditionDate() > $now;
        });

        return array_values($itemsQueue);
    }
}

class_alias(ConfigurationDataGenerator::class, 'EzSystems\EzPlatformPageBuilder\Block\ScheduleBlock\ConfigurationDataGenerator');
