<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\ScheduleBlock;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\ScheduleBlock\Item\Item;
use Ibexa\FieldTypePage\ScheduleBlock\Item\UnavailableLocation;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 */
class ScheduleService
{
    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     *
     * @throws \Exception
     */
    public function initializeScheduleData(BlockValue $blockValue): void
    {
        $limit = $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_LIMIT)->getValue();

        $slots = [];
        for ($i = 0; $i < $limit; ++$i) {
            $slots[] = new Slot(sprintf('sbs-%s', Uuid::uuid1()->toString()), null);
        }

        $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_SLOTS)->setValue($slots);
        $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS)->setValue([]);
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param int $number
     *
     * @throws \Exception
     */
    public function changeSlotsNumber(BlockValue $blockValue, int $number): void
    {
        $slots = $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_SLOTS)->getValue();

        $newSlots = [];
        for ($i = 0; $i < $number; ++$i) {
            $newSlots[] = $slots[$i] ?? new Slot(Uuid::uuid1()->toString());
        }

        $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_SLOTS)->setValue($newSlots);
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param string $itemId
     */
    public function removeItem(BlockValue $blockValue, string $itemId): void
    {
        $items = $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS)->getValue();

        unset($items[$itemId]);

        $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS)->setValue($items);
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Item\Item $item
     */
    public function addItem(BlockValue $blockValue, Item $item): void
    {
        $initialItems = $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS)->getValue();
        $initialItems = [$item->getId() => $item] + $initialItems;
        $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS)->setValue($initialItems);
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $scheduleBlock
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Item\Item[] $items
     */
    public function arrangeItems(BlockValue $scheduleBlock, array $items): void
    {
        $items = array_filter(
            $items,
            static function (Item $item): bool {
                return !$item->getLocation() instanceof UnavailableLocation;
            }
        );

        /** @var \Ibexa\FieldTypePage\ScheduleBlock\Slot $slot */
        foreach ($scheduleBlock->getAttribute(ScheduleBlock::ATTRIBUTE_SLOTS)->getValue() as $slot) {
            $slot->setItem(array_shift($items));
        }
    }

    /**
     * @deprecated Deprecated since 1.1 and will be removed in 2.0.
     *
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Item\Item[] $items
     *
     * @return \Ibexa\FieldTypePage\ScheduleBlock\Item\Item[]
     */
    public function distributeItems(BlockValue $blockValue, array $items): array
    {
        $this->arrangeItems($blockValue, $items);

        $itemsFromSlots = [];
        /** @var \Ibexa\FieldTypePage\ScheduleBlock\Slot $slot */
        foreach ($blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_SLOTS)->getValue() as $slot) {
            if (null === $slot->getItem()) {
                continue;
            }

            $itemsFromSlots[] = $slot->getItem();
        }

        // return leftover items for BC
        return array_diff($items, $itemsFromSlots);
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $scheduleBlock
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue
     */
    public function resetScheduleBlock(BlockValue $scheduleBlock): BlockValue
    {
        $resetAttributes = [
            ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS => [],
        ];

        $loadedSnapshot = $scheduleBlock->getAttribute(ScheduleBlock::ATTRIBUTE_LOADED_SNAPSHOT)->getValue();
        if (null !== $loadedSnapshot) {
            $resetAttributes[ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS] = $loadedSnapshot->getInitialItems();
        }

        $scheduleBlock->setAttributes(
            array_replace($scheduleBlock->getAttributes(), $resetAttributes)
        );

        foreach ($scheduleBlock->getAttribute(ScheduleBlock::ATTRIBUTE_SLOTS)->getValue() as $slot) {
            $slot->setItem(null);
        }

        return $scheduleBlock;
    }
}

class_alias(ScheduleService::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\ScheduleService');
