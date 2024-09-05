<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Segmentation\Event;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Segmentation\Value\Segment;

final class BeforeRemoveSegmentEvent extends BeforeEvent
{
    private Segment $segment;

    public function __construct(Segment $segment)
    {
        $this->segment = $segment;
    }

    public function getSegment(): Segment
    {
        return $this->segment;
    }
}
