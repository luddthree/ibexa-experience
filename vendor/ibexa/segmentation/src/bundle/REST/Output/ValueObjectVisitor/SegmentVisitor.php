<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class SegmentVisitor extends ValueObjectVisitor
{
    private const OBJECT_SEGMENT = 'Segment';
    private const OBJECT_SEGMENT_GROUP = 'SegmentGroup';

    /**
     * @param \Ibexa\Bundle\Segmentation\REST\Value\Segment $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_SEGMENT);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_SEGMENT));
        $generator->attribute(
            'href',
            $this->router->generate('ibexa.segmentation.rest.segments.view', [
                'identifier' => $data->segment->identifier,
            ]),
        );

        $generator->valueElement('id', $data->segment->id);
        $generator->valueElement('identifier', $data->segment->identifier);
        $generator->valueElement('name', $data->segment->name);

        $generator->startObjectElement(self::OBJECT_SEGMENT_GROUP);
        $generator->attribute(
            'href',
            $this->router->generate(
                'ibexa.segmentation.rest.segment_groups.view',
                [
                    'identifier' => $data->segment->group->identifier,
                ],
            ),
        );
        $generator->endObjectElement(self::OBJECT_SEGMENT_GROUP);

        $generator->endObjectElement(self::OBJECT_SEGMENT);
    }
}
