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

final class UserSegmentsListVisitor extends ValueObjectVisitor
{
    private const OBJECT_USER_SEGMENT_LIST = 'UserSegmentList';
    private const OBJECT_USER_SEGMENT = 'UserSegment';

    /**
     * @param \Ibexa\Bundle\Segmentation\REST\Value\UserSegmentsList $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_USER_SEGMENT_LIST);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_USER_SEGMENT_LIST));

        $generator->attribute(
            'href',
            $this->router->generate('ibexa.segmentation.rest.user.segments.view', [
                'userId' => $data->user->getUserId(),
            ]),
        );

        $generator->startList(self::OBJECT_USER_SEGMENT);
        foreach ($data->segments as $segment) {
            $visitor->visitValueObject($segment);
        }
        $generator->endList(self::OBJECT_USER_SEGMENT);

        $generator->endObjectElement(self::OBJECT_USER_SEGMENT_LIST);
    }
}
