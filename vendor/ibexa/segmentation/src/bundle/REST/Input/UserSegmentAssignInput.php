<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\REST\Input;

final class UserSegmentAssignInput
{
    /** @var \Ibexa\Segmentation\Value\Segment[] */
    public array $segments;

    /**
     * @param \Ibexa\Segmentation\Value\Segment[] $segments
     */
    public function __construct(array $segments)
    {
        $this->segments = $segments;
    }
}
