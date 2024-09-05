<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Data;

final class SegmentBulkDeleteData
{
    /** @var array|null */
    private $segments;

    public function __construct(array $segments = [])
    {
        $this->segments = $segments;
    }

    public function getSegments(): array
    {
        return $this->segments;
    }

    public function setSegments(array $segments): void
    {
        $this->segments = $segments;
    }
}

class_alias(SegmentBulkDeleteData::class, 'Ibexa\Platform\Segmentation\Data\SegmentBulkDeleteData');
