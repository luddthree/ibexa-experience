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

final class SegmentGroupVisitor extends ValueObjectVisitor
{
    private const OBJECT_SEGMENT_GROUP = 'SegmentGroup';

    /**
     * @param \Ibexa\Bundle\Segmentation\REST\Value\SegmentGroup $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_SEGMENT_GROUP);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_SEGMENT_GROUP));
        $generator->attribute(
            'href',
            $this->router->generate('ibexa.segmentation.rest.segment_groups.view', [
                'identifier' => $data->segmentGroup->identifier,
            ]),
        );

        $generator->valueElement('id', $data->segmentGroup->id);
        $generator->valueElement('identifier', $data->segmentGroup->identifier);
        $generator->valueElement('name', $data->segmentGroup->name);

        $generator->endObjectElement(self::OBJECT_SEGMENT_GROUP);
    }
}
