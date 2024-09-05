<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\Segments;

use Ibexa\Personalization\Value\Model\SegmentsStruct;

/**
 * @internal
 *
 * @phpstan-type T array{
 *      segmentItemGroups: array<array{
 *          id: int,
 *          groupElements: array<array{
 *              id: int,
 *              mainSegment: array{
 *                  segment: string,
 *                  isActive: bool,
 *              },
 *              childSegments: array<array{
 *                  segment: string,
 *                  isActive: bool,
 *              }>,
 *              childGroupingOperation: \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*,
 *          }>,
 *          groupingOperation: \Ibexa\Personalization\Client\Consumer\Model\GroupingOperation::*,
 *      }>,
 *      activeSegments: array<string>,
 *      maximumRatingAge: string,
 * }
 */
interface SegmentsStructFactoryInterface
{
    /**
     * @phpstan-param T $responseContents
     */
    public function createSegmentsStruct(array $responseContents): SegmentsStruct;
}
