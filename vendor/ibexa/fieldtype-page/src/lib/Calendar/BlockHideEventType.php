<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Calendar;

use Ibexa\Calendar\EventAction\EventActionCollection;
use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

class BlockHideEventType implements EventTypeInterface
{
    private const EVENT_TYPE_IDENTIFIER = 'page_block_hide';

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Ibexa\Contracts\Calendar\EventCollection */
    private $actions;

    public function __construct(iterable $actions, TranslatorInterface $translator)
    {
        $this->actions = new EventActionCollection($actions);
        $this->translator = $translator;
    }

    public function getTypeIdentifier(): string
    {
        return self::EVENT_TYPE_IDENTIFIER;
    }

    public function getTypeLabel(): string
    {
        return $this->translator->trans(
            /** @Desc("Future hide") */
            'page_block_hide.label',
            [],
            'ibexa_calendar_events'
        );
    }

    public function getEventName(Event $event): string
    {
        return $event->getBlockName();
    }

    public function getActions(): EventActionCollection
    {
        return $this->actions;
    }
}

class_alias(BlockHideEventType::class, 'EzSystems\EzPlatformPageFieldType\Calendar\BlockHideEventType');
