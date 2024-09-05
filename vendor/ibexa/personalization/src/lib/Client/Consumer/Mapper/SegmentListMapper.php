<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Mapper;

use Ibexa\Personalization\Value\Model\Segment;
use Ibexa\Personalization\Value\Model\SegmentList;

final class SegmentListMapper implements SegmentListMapperInterface
{
    public function map(SegmentList $segmentList): array
    {
        return array_map(
            static function (Segment $segment) {
                return [
                    'id' => $segment->getId(),
                    'name' => $segment->getName(),
                    'group' => [
                        'id' => $segment->getGroup()->getId(),
                        'name' => $segment->getGroup()->getName(),
                    ],
                ];
            },
            $segmentList->getSegments()
        );
    }
}
