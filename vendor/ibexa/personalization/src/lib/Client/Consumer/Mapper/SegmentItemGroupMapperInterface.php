<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Mapper;

use Ibexa\Personalization\Value\Model\SegmentItemGroup;

/**
 * @phpstan-import-type R from \Ibexa\Personalization\Client\Consumer\Mapper\SegmentDataMapperInterface as RSegmentData
 *
 * @phpstan-type R array{
 *     id: int,
 *     groupingOperation: \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*,
 *     groupElements: array<array{
 *         id: int,
 *         mainSegment: RSegmentData,
 *         childSegments: array<RSegmentData>,
 *         childGroupingOperation: \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*,
 *     }>,
 * }
 */
interface SegmentItemGroupMapperInterface
{
    /**
     * @return R
     */
    public function map(SegmentItemGroup $segmentItemGroup): array;
}
