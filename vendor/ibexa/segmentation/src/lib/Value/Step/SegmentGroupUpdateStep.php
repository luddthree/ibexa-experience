<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Value\Step;

use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;

final class SegmentGroupUpdateStep implements StepInterface
{
    /** @var \Ibexa\Segmentation\Value\SegmentGroupMatcher */
    private $matcher;

    /** @var string|null */
    private $name;

    /** @var string|null */
    private $identifier;

    public function __construct(
        SegmentGroupMatcher $matcher,
        ?string $identifier = null,
        ?string $name = null
    ) {
        $this->matcher = $matcher;
        $this->identifier = $identifier;
        $this->name = $name;
    }

    public function getMatcher(): SegmentGroupMatcher
    {
        return $this->matcher;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }
}
