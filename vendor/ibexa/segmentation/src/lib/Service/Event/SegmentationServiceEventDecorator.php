<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Service\Event;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Segmentation\Event\AssignUserToSegmentEvent;
use Ibexa\Contracts\Segmentation\Event\BeforeAssignUserToSegmentEvent;
use Ibexa\Contracts\Segmentation\Event\BeforeCreateSegmentEvent;
use Ibexa\Contracts\Segmentation\Event\BeforeCreateSegmentGroupEvent;
use Ibexa\Contracts\Segmentation\Event\BeforeRemoveSegmentEvent;
use Ibexa\Contracts\Segmentation\Event\BeforeRemoveSegmentGroupEvent;
use Ibexa\Contracts\Segmentation\Event\BeforeUnassignUserFromSegmentEvent;
use Ibexa\Contracts\Segmentation\Event\BeforeUpdateSegmentEvent;
use Ibexa\Contracts\Segmentation\Event\BeforeUpdateSegmentGroupEvent;
use Ibexa\Contracts\Segmentation\Event\CreateSegmentEvent;
use Ibexa\Contracts\Segmentation\Event\CreateSegmentGroupEvent;
use Ibexa\Contracts\Segmentation\Event\RemoveSegmentEvent;
use Ibexa\Contracts\Segmentation\Event\RemoveSegmentGroupEvent;
use Ibexa\Contracts\Segmentation\Event\UnassignUserFromSegmentEvent;
use Ibexa\Contracts\Segmentation\Event\UpdateSegmentEvent;
use Ibexa\Contracts\Segmentation\Event\UpdateSegmentGroupEvent;
use Ibexa\Contracts\Segmentation\SegmentationServiceDecorator;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroup;
use Ibexa\Segmentation\Value\SegmentGroupCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroupUpdateStruct;
use Ibexa\Segmentation\Value\SegmentUpdateStruct;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SegmentationServiceEventDecorator extends SegmentationServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(SegmentationServiceInterface $innerService, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($innerService);
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createSegment(SegmentCreateStruct $createStruct): Segment
    {
        $beforeEvent = new BeforeCreateSegmentEvent($createStruct, null);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getSegmentResult();
        }

        $segmentResult = $beforeEvent->hasSegmentResult()
            ? $beforeEvent->getSegmentResult()
            : $this->innerService->createSegment($createStruct);

        $this->eventDispatcher->dispatch(new CreateSegmentEvent($createStruct, $segmentResult));

        return $segmentResult;
    }

    public function updateSegment($segment, SegmentUpdateStruct $updateStruct): Segment
    {
        $beforeEvent = new BeforeUpdateSegmentEvent($segment, $updateStruct, null);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getSegmentResult();
        }

        $segmentResult = $beforeEvent->hasSegmentResult()
            ? $beforeEvent->getSegmentResult()
            : $this->innerService->updateSegment($segment, $updateStruct);

        $this->eventDispatcher->dispatch(new UpdateSegmentEvent($segment, $updateStruct, $segmentResult));

        return $segmentResult;
    }

    public function removeSegment(Segment $segment): void
    {
        $beforeEvent = new BeforeRemoveSegmentEvent($segment);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->removeSegment($segment);

        $this->eventDispatcher->dispatch(new RemoveSegmentEvent($segment));
    }

    public function createSegmentGroup(SegmentGroupCreateStruct $createStruct): SegmentGroup
    {
        $beforeEvent = new BeforeCreateSegmentGroupEvent($createStruct, null);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getSegmentGroupResult();
        }

        $segmentGroupResult = $beforeEvent->hasSegmentGroupResult()
            ? $beforeEvent->getSegmentGroupResult()
            : $this->innerService->createSegmentGroup($createStruct);

        $this->eventDispatcher->dispatch(new CreateSegmentGroupEvent($createStruct, $segmentGroupResult));

        return $segmentGroupResult;
    }

    public function updateSegmentGroup($segmentGroup, SegmentGroupUpdateStruct $updateStruct): SegmentGroup
    {
        $beforeEvent = new BeforeUpdateSegmentGroupEvent($segmentGroup, $updateStruct, null);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getSegmentGroupResult();
        }

        $segmentGroupResult = $beforeEvent->hasSegmentGroupResult()
            ? $beforeEvent->getSegmentGroupResult()
            : $this->innerService->updateSegmentGroup($segmentGroup, $updateStruct);

        $this->eventDispatcher->dispatch(new UpdateSegmentGroupEvent($segmentGroup, $updateStruct, $segmentGroupResult));

        return $segmentGroupResult;
    }

    public function removeSegmentGroup(SegmentGroup $segmentGroup): void
    {
        $beforeEvent = new BeforeRemoveSegmentGroupEvent($segmentGroup);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->removeSegmentGroup($segmentGroup);

        $this->eventDispatcher->dispatch(new RemoveSegmentGroupEvent($segmentGroup));
    }

    public function assignUserToSegment(User $user, Segment $segment): void
    {
        $beforeEvent = new BeforeAssignUserToSegmentEvent($user, $segment);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->assignUserToSegment($user, $segment);

        $this->eventDispatcher->dispatch(new AssignUserToSegmentEvent($user, $segment));
    }

    public function unassignUserFromSegment(User $user, Segment $segment): void
    {
        $beforeEvent = new BeforeUnassignUserFromSegmentEvent($user, $segment);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->unassignUserFromSegment($user, $segment);

        $this->eventDispatcher->dispatch(new UnassignUserFromSegmentEvent($user, $segment));
    }
}
