<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Mapper;

use Ibexa\Segmentation\Value\Segment;

/**
 * @internal
 */
interface SegmentMapperInterface
{
    /**
     * @param string[] $segments
     *
     * @return array<int, \Ibexa\Segmentation\Value\Segment>
     */
    public function getMapping(array $segments): array;

    /**
     * @param array<string|int> $segments
     * @param array<int, \Ibexa\Segmentation\Value\Segment> $segmentsMapping
     *
     * @return array<int, \Ibexa\Segmentation\Value\Segment>
     */
    public function mapSegments(array $segments, array $segmentsMapping): array;

    /**
     * @param array<int, \Ibexa\Segmentation\Value\Segment> $segmentsMapping
     */
    public function mapSegment(int $key, array $segmentsMapping): ?Segment;
}
