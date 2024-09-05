<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\REST\ValueObjectVisitor;

use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

abstract class AbstractEventVisitor extends ValueObjectVisitor
{
    /**
     * @param \Ibexa\Contracts\Rest\Output\Visitor $visitor
     * @param \Ibexa\Contracts\Rest\Output\Generator $generator
     * @param \Ibexa\Contracts\Calendar\Event $data
     */
    final public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $className = $this->getEventClassName($data);

        $generator->startObjectElement($className);

        $this->visitId($visitor, $generator, $data);
        $this->visitDateTime($visitor, $generator, $data);
        $this->visitType($visitor, $generator, $data);
        $this->visitName($visitor, $generator, $data);

        $generator->startHashElement('attributes');
        $this->visitAttributes($visitor, $generator, $data);
        $generator->endHashElement('attributes');

        $generator->endObjectElement($className);
    }

    protected function visitAttributes(Visitor $visitor, Generator $generator, Event $event): void
    {
    }

    private function visitId(Visitor $visitor, Generator $generator, Event $event): void
    {
        $generator->startValueElement('id', $event->getId());
        $generator->endValueElement('id');
    }

    private function visitDateTime(Visitor $visitor, Generator $generator, Event $event): void
    {
        $generator->startValueElement('dateTime', $event->getDateTime()->format('U'));
        $generator->endValueElement('dateTime');
    }

    private function visitType(Visitor $visitor, Generator $generator, Event $event): void
    {
        $generator->startValueElement('type', $event->getType()->getTypeIdentifier());
        $generator->endValueElement('type');
    }

    private function visitName(Visitor $visitor, Generator $generator, Event $event): void
    {
        $generator->startValueElement('name', $event->getName());
        $generator->endValueElement('name');
    }

    private function getEventClassName(Event $event): string
    {
        $className = get_class($event);
        if (($pos = strrpos($className, '\\')) !== false) {
            return substr($className, $pos + 1);
        }

        return $className;
    }
}

class_alias(AbstractEventVisitor::class, 'EzSystems\EzPlatformCalendarBundle\REST\ValueObjectVisitor\AbstractEventVisitor');
