<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Value\Step;

use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;

final class SegmentGroupDeleteStep implements StepInterface
{
    /** @var \Ibexa\Segmentation\Value\SegmentGroupMatcher */
    private $matcher;

    public function __construct(SegmentGroupMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    public function getMatcher(): SegmentGroupMatcher
    {
        return $this->matcher;
    }
}
