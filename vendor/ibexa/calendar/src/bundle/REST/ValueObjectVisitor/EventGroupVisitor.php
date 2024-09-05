<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\REST\ValueObjectVisitor;

use Ibexa\Contracts\Calendar\EventGroup;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\Visitor;

final class EventGroupVisitor extends AbstractEventListVisitor
{
    /**
     * @param \Ibexa\Contracts\Rest\Output\Visitor $visitor
     * @param \Ibexa\Contracts\Rest\Output\Generator $generator
     * @param \Ibexa\Contracts\Calendar\EventGroup $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $visitor->setHeader('Content-Type', $generator->getMediaType('EventGroup'));

        $generator->startObjectElement('EventGroup');

        $this->generateCurrentPageUrlAttribute($generator, $data);
        $this->generateNextPageUrlAttribute($generator, $data);
        $this->visitGroupKey($visitor, $generator, $data);
        $this->visitTotalCount($generator, $data);
        $this->visitEvents($visitor, $generator, $data);

        $generator->endObjectElement('EventGroup');
    }

    private function visitGroupKey(Visitor $visitor, Generator $generator, EventGroup $group): void
    {
        $visitor->visitValueObject($group->getGroupKey());
    }
}

class_alias(EventGroupVisitor::class, 'EzSystems\EzPlatformCalendarBundle\REST\ValueObjectVisitor\EventGroupVisitor');
