<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Mapper;

use Ibexa\Personalization\Value\Model\SegmentList;

/**
 * @phpstan-type R array<array{
 *     id: int,
 *     name: string,
 *     group: array{
 *         id: int,
 *         name: string,
 *     },
 * }>
 */
interface SegmentListMapperInterface
{
    /**
     * @return R
     */
    public function map(SegmentList $segmentList): array;
}
