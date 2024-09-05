<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class PersonalizationSegmentsResolverEvent extends Event
{
    /** @var int[] */
    private array $segmentsIds;

    /** @var array<int, \Ibexa\Segmentation\Value\Segment> */
    private array $segmentsMapping = [];

    /**
     * @param int[] $segmentsIds
     */
    public function __construct(array $segmentsIds = [])
    {
        $this->segmentsIds = $segmentsIds;
    }

    /**
     * @return int[]
     */
    public function getSegmentsIds(): array
    {
        return $this->segmentsIds;
    }

    /**
     * @return array<int, \Ibexa\Segmentation\Value\Segment>
     */
    public function getSegmentsMapping(): array
    {
        return $this->segmentsMapping;
    }

    /**
     * @param array<int, \Ibexa\Segmentation\Value\Segment> $segmentsMapping
     */
    public function setSegmentsMapping(array $segmentsMapping): void
    {
        $this->segmentsMapping = $segmentsMapping;
    }
}
