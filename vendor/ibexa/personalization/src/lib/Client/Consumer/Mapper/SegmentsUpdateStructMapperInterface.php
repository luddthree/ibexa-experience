<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Mapper;

use Ibexa\Personalization\Value\Model\SegmentsUpdateStruct;

/**
 * @phpstan-import-type R from \Ibexa\Personalization\Client\Consumer\Mapper\SegmentListMapperInterface as RSegmentList
 * @phpstan-import-type R from \Ibexa\Personalization\Client\Consumer\Mapper\SegmentItemGroupMapperInterface as RSegmentItemGroup
 *
 * @phpstan-type R array{
 *     segmentItemGroups: array<RSegmentItemGroup>,
 *     activeSegments: RSegmentList,
 *     eventType: string,
 *     maximumRatingAge: string,
 * }
 */
interface SegmentsUpdateStructMapperInterface
{
    /**
     * @return R
     */
    public function map(SegmentsUpdateStruct $segmentsUpdateStruct): array;
}
