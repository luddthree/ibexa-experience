<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\REST\Value;

use Ibexa\Rest\Value;
use Ibexa\Segmentation\Value\Segment as SegmentContract;

final class Segment extends Value
{
    public SegmentContract $segment;

    public function __construct(SegmentContract $segment)
    {
        $this->segment = $segment;
    }
}
