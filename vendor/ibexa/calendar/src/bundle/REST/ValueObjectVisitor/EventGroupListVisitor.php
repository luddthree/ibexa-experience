<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\REST\ValueObjectVisitor;

use Ibexa\Contracts\Calendar\EventGroupList;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class EventGroupListVisitor extends ValueObjectVisitor
{
    /**
     * @param \Ibexa\Contracts\Rest\Output\Visitor $visitor
     * @param \Ibexa\Contracts\Rest\Output\Generator $generator
     * @param \Ibexa\Contracts\Calendar\EventGroupList $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $visitor->setHeader('Content-Type', $generator->getMediaType('EventGroupList'));

        $generator->startObjectElement('EventGroupList');
        $this->visitGroups($visitor, $generator, $data);
        $generator->endObjectElement('EventGroupList');
    }

    private function visitGroups(Visitor $visitor, Generator $generator, EventGroupList $data): void
    {
        $generator->startList('groups');
        foreach ($data->getGroups() as $event) {
            $visitor->visitValueObject($event);
        }
        $generator->endList('groups');
    }
}

class_alias(EventGroupListVisitor::class, 'EzSystems\EzPlatformCalendarBundle\REST\ValueObjectVisitor\EventGroupListVisitor');
