<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Mapper;

use Ibexa\Personalization\Value\Model\SegmentData;

final class SegmentDataMapper implements SegmentDataMapperInterface
{
    public function map(SegmentData $segmentData): array
    {
        return [
            'segment' => [
                'id' => $segmentData->getSegment()->getId(),
                'name' => $segmentData->getSegment()->getName(),
                'group' => [
                    'id' => $segmentData->getSegment()->getGroup()->getId(),
                    'name' => $segmentData->getSegment()->getGroup()->getName(),
                ],
            ],
            'active' => $segmentData->isActive(),
        ];
    }
}
