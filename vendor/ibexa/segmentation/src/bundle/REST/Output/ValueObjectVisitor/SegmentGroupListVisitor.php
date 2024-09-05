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

final class SegmentGroupListVisitor extends ValueObjectVisitor
{
    private const OBJECT_SEGMENT_GROUP_LIST = 'SegmentGroupList';
    private const OBJECT_SEGMENT_GROUP = 'SegmentGroup';

    /**
     * @param \Ibexa\Bundle\Segmentation\REST\Value\SegmentGroupList $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_SEGMENT_GROUP_LIST);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_SEGMENT_GROUP_LIST));
        $generator->attribute(
            'href',
            $this->router->generate('ibexa.segmentation.rest.segment_groups.list'),
        );

        $generator->startList(self::OBJECT_SEGMENT_GROUP);
        foreach ($data->segmentGroups as $segmentGroup) {
            $visitor->visitValueObject($segmentGroup);
        }
        $generator->endList(self::OBJECT_SEGMENT_GROUP);

        $generator->endObjectElement(self::OBJECT_SEGMENT_GROUP_LIST);
    }
}
