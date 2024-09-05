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

final class SegmentListVisitor extends ValueObjectVisitor
{
    private const OBJECT_SEGMENT_LIST = 'SegmentList';
    private const OBJECT_SEGMENT = 'Segment';

    /**
     * @param \Ibexa\Bundle\Segmentation\REST\Value\SegmentList $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_SEGMENT_LIST);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_SEGMENT_LIST));
        $generator->attribute(
            'href',
            $this->router->generate('ibexa.segmentation.rest.segment_groups.list.segments', [
                'identifier' => $data->segmentGroup->segmentGroup->identifier,
            ]),
        );

        $generator->startList(self::OBJECT_SEGMENT);
        foreach ($data->segments as $segment) {
            $visitor->visitValueObject($segment);
        }
        $generator->endList(self::OBJECT_SEGMENT);

        $generator->endObjectElement(self::OBJECT_SEGMENT_LIST);
    }
}
