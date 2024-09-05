<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

use JsonSerializable;

final class SegmentData implements JsonSerializable
{
    private Segment $segment;

    private bool $active = true;

    public function __construct(Segment $segment, bool $active)
    {
        $this->segment = $segment;
        $this->active = $active;
    }

    public function getSegment(): Segment
    {
        return $this->segment;
    }

    public function setSegment(Segment $segment): void
    {
        $this->segment = $segment;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @phpstan-return array{
     *      'segment': Segment,
     *      'isActive': bool
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'segment' => $this->segment,
            'isActive' => $this->active,
        ];
    }
}
