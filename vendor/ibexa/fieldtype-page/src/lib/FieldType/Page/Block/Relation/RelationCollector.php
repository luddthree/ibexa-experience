<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Relation;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\Event\BlockRelationEvents;
use Ibexa\FieldTypePage\Event\CollectBlockRelationsEvent;
use Ibexa\FieldTypePage\FieldType\LandingPage\Value;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RelationCollector
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Value $fieldValue
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     *
     * @return int[]
     */
    public function collect(Value $fieldValue, BlockValue $blockValue): array
    {
        $event = new CollectBlockRelationsEvent($fieldValue, $blockValue);
        $this->eventDispatcher->dispatch($event, BlockRelationEvents::COLLECT_BLOCK_RELATIONS);
        $this->eventDispatcher->dispatch($event, BlockRelationEvents::getCollectBlockRelationsEventName($blockValue->getType()));

        return array_unique($event->getRelations());
    }
}

class_alias(RelationCollector::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Relation\RelationCollector');
