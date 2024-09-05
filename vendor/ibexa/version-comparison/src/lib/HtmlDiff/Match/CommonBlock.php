<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Match;

final class CommonBlock
{
    /** @var int */
    private $startInOld;

    /** @var int */
    private $startInNew;

    /** @var int */
    private $length;

    /** @var \Ibexa\VersionComparison\HtmlDiff\Match\Segment */
    private $segment;

    /** @var int */
    private $endInOld;

    /** @var int */
    private $endInNew;

    /** @var int */
    private $segmentStartInOld;

    /** @var int */
    private $segmentStartInNew;

    /** @var int */
    private $segmentEndInNew;

    /** @var int */
    private $segmentEndInOld;

    public function __construct(int $startInOld, int $startInNew, int $length, Segment $segment)
    {
        $this->length = $length;
        $this->segment = $segment;

        $this->startInOld = $startInOld + $segment->getOldIndex();
        $this->startInNew = $startInNew + $segment->getNewIndex();

        $this->endInOld = $this->startInOld + $length - 1;
        $this->endInNew = $this->startInNew + $length - 1;

        $this->segmentStartInOld = $startInOld;
        $this->segmentStartInNew = $startInNew;

        $this->segmentEndInOld = $this->segmentStartInOld + $length - 1;
        $this->segmentEndInNew = $this->segmentStartInNew + $length - 1;
    }

    public function startInOld(): int
    {
        return $this->startInOld;
    }

    public function startInNew(): int
    {
        return $this->startInNew;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getSegment(): Segment
    {
        return $this->segment;
    }

    public function endInOld(): int
    {
        return $this->endInOld;
    }

    public function endInNew(): int
    {
        return $this->endInNew;
    }

    public function segmentStartInOld(): int
    {
        return $this->segmentStartInOld;
    }

    public function segmentStartInNew(): int
    {
        return $this->segmentStartInNew;
    }

    public function segmentEndInNew(): int
    {
        return $this->segmentEndInNew;
    }

    public function segmentEndInOld(): int
    {
        return $this->segmentEndInOld;
    }
}

class_alias(CommonBlock::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Match\Match');

class_alias(CommonBlock::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Match\CommonBlock');
