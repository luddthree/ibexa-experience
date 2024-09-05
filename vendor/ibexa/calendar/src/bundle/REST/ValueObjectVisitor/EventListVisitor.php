<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\REST\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\Visitor;

final class EventListVisitor extends AbstractEventListVisitor
{
    /**
     * @param \Ibexa\Contracts\Rest\Output\Visitor $visitor
     * @param \Ibexa\Contracts\Rest\Output\Generator $generator
     * @param \Ibexa\Contracts\Calendar\EventList $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $visitor->setHeader('Content-Type', $generator->getMediaType('EventList'));

        $generator->startObjectElement('EventList');

        $this->generateCurrentPageUrlAttribute($generator, $data);
        $this->generateNextPageUrlAttribute($generator, $data);
        $this->visitTotalCount($generator, $data);
        $this->visitEvents($visitor, $generator, $data);

        $generator->endObjectElement('EventList');
    }
}

class_alias(EventListVisitor::class, 'EzSystems\EzPlatformCalendarBundle\REST\ValueObjectVisitor\EventListVisitor');
