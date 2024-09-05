<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Value\Step;

use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Value\SegmentMatcher;

final class SegmentDeleteStep implements StepInterface
{
    /** @var \Ibexa\Segmentation\Value\SegmentMatcher */
    private $matcher;

    public function __construct(SegmentMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    public function getMatcher(): SegmentMatcher
    {
        return $this->matcher;
    }
}
