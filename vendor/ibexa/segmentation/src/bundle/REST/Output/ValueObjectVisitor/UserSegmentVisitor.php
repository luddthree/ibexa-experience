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

final class UserSegmentVisitor extends ValueObjectVisitor
{
    private const OBJECT_USER_SEGMENT = 'UserSegment';

    /**
     * @param \Ibexa\Bundle\Segmentation\REST\Value\UserSegment $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_USER_SEGMENT);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_USER_SEGMENT));

        $segment = $data->segment;
        $user = $data->user;

        $generator->valueElement('id', $segment->id);
        $generator->valueElement('identifier', $segment->identifier);
        $generator->valueElement('name', $segment->name);

        $generator->startObjectElement('SegmentGroup');
        $generator->attribute(
            'href',
            $this->router->generate(
                'ibexa.segmentation.rest.segment_groups.view',
                [
                    'identifier' => $segment->group->identifier,
                ],
            ),
        );
        $generator->endObjectElement('SegmentGroup');

        $generator->startObjectElement('User');
        $generator->attribute(
            'href',
            $this->router->generate('ibexa.rest.load_user', ['userId' => $user->getUserId()])
        );
        $generator->endObjectElement('User');

        $generator->endObjectElement(self::OBJECT_USER_SEGMENT);
    }
}
