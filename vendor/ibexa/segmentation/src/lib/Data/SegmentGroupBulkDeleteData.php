<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Data;

final class SegmentGroupBulkDeleteData
{
    /** @var array|null */
    private $segmentGroups;

    public function __construct(array $segmentGroups = [])
    {
        $this->segmentGroups = $segmentGroups;
    }

    public function getSegmentGroups(): array
    {
        return $this->segmentGroups;
    }

    public function setSegmentGroups(array $segmentGroups): void
    {
        $this->segmentGroups = $segmentGroups;
    }
}

class_alias(SegmentGroupBulkDeleteData::class, 'Ibexa\Platform\Segmentation\Data\SegmentGroupBulkDeleteData');
