<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\REST\Server\Output\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

class NotScheduled extends ValueObjectVisitor
{
    /**
     * Visit struct returned by controllers.
     *
     * @param \Ibexa\Contracts\Rest\Output\Visitor $visitor
     * @param \Ibexa\Contracts\Rest\Output\Generator $generator
     * @param \Ibexa\Contracts\Scheduler\ValueObject\NotScheduledEntry $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        $visitor->setHeader('Content-Type', $generator->getMediaType('NotScheduled'));
        $visitor->setHeader('Accept-Patch', false);

        $generator->startObjectElement('NotScheduled');
        $generator->endObjectElement('NotScheduled');
    }
}

class_alias(NotScheduled::class, 'EzSystems\DateBasedPublisher\Core\REST\Server\Output\ValueObjectVisitor\NotScheduled');
