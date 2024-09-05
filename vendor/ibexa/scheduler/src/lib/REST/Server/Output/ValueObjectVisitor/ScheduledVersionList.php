<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\REST\Server\Output\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

class ScheduledVersionList extends ValueObjectVisitor
{
    /**
     * Visit struct returned by controllers.
     *
     * @param \Ibexa\Contracts\Rest\Output\Visitor $visitor
     * @param \Ibexa\Contracts\Rest\Output\Generator $generator
     * @param \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntryList $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        $visitor->setHeader('Content-Type', $generator->getMediaType('ScheduledVersionList'));
        $visitor->setHeader('Accept-Patch', false);

        $generator->startObjectElement('ScheduledVersionList');

        $generator->startValueElement('page', $data->page);
        $generator->endValueElement('page');

        $generator->startValueElement('limit', $data->limit);
        $generator->endValueElement('limit');

        $generator->startValueElement('total', $data->total);
        $generator->endValueElement('total');

        $generator->startList('scheduled');
        foreach ($data->scheduledEntries as $scheduledVersion) {
            $visitor->visitValueObject($scheduledVersion);
        }
        $generator->endList('scheduled');

        $generator->endObjectElement('ScheduledVersionList');
    }
}

class_alias(ScheduledVersionList::class, 'EzSystems\DateBasedPublisher\Core\REST\Server\Output\ValueObjectVisitor\ScheduledVersionList');
