<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Personalization\Event\PersonalizationSegmentsResolverEvent;
use Ibexa\Personalization\Event\PersonalizationUserTrackingRenderOptionsEvent;
use Ibexa\Segmentation\Exception\Persistence\SegmentNotFoundException;
use Ibexa\Segmentation\Value\Segment;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PersonalizationUserSegmentsSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const SEGMENTS_KEY = 'segments';

    private Repository $repository;

    private SegmentationServiceInterface $segmentationService;

    public function __construct(
        Repository $repository,
        SegmentationServiceInterface $segmentationService,
        ?LoggerInterface $logger = null
    ) {
        $this->repository = $repository;
        $this->segmentationService = $segmentationService;
        $this->logger = $logger ?? new NullLogger();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PersonalizationUserTrackingRenderOptionsEvent::class => ['onUserTrackingRender', 255],
            PersonalizationSegmentsResolverEvent::class => ['onSegmentsResolver', 255],
        ];
    }

    public function onUserTrackingRender(PersonalizationUserTrackingRenderOptionsEvent $event): void
    {
        $segments = array_column(
            $this->segmentationService->loadSegmentsAssignedToCurrentUser(),
            'id'
        );

        $previousSegments = $event->getOptions()[self::SEGMENTS_KEY] ?? [];
        $event->setOption(self::SEGMENTS_KEY, array_merge($previousSegments, $segments));
    }

    public function onSegmentsResolver(PersonalizationSegmentsResolverEvent $event): void
    {
        $resultMapping = $event->getSegmentsMapping();

        foreach ($event->getSegmentsIds() as $id) {
            try {
                $resultMapping[$id] = $this->repository->sudo(
                    fn (): Segment => $this->segmentationService->loadSegment($id)
                );
            } catch (SegmentNotFoundException $e) {
                $this->logger->debug(sprintf('Segment not found with id: %s', $id));
            }
        }

        $event->setSegmentsMapping($resultMapping);
    }
}
