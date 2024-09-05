<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Mapper;

use Ibexa\Personalization\Event\PersonalizationSegmentsResolverEvent;
use Ibexa\Segmentation\Value\Segment;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class SegmentMapper implements SegmentMapperInterface
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapping(array $segments): array
    {
        $segmentsIds = array_filter(
            array_map('intval', $segments),
            static fn (int $id): bool => $id > 0
        );

        $event = new PersonalizationSegmentsResolverEvent($segmentsIds);
        $this->eventDispatcher->dispatch($event);

        return $event->getSegmentsMapping();
    }

    public function mapSegments(array $segments, array $segmentsMapping): array
    {
        $resultMap = [];

        foreach ($segments as $segmentId) {
            $key = (int) $segmentId;
            $segment = $this->mapSegment($key, $segmentsMapping);

            if (null === $segment) {
                continue;
            }

            $resultMap[$key] = $segment;
        }

        return $resultMap;
    }

    public function mapSegment(int $key, array $segmentsMapping): ?Segment
    {
        if ($key === 0 || !array_key_exists($key, $segmentsMapping)) {
            return null;
        }

        return $segmentsMapping[$key];
    }
}
