<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Workflow\WorkflowTimeline;

use DateTimeInterface;
use Ibexa\Contracts\Workflow\Event\TimelineEvent;
use Ibexa\Contracts\Workflow\Event\TimelineEvents;
use Ibexa\Workflow\Value\WorkflowMetadata;
use Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry;
use Ibexa\Workflow\WorkflowTimeline\Value\WorkflowTimeline;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WorkflowTimelineFactory
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata $workflowMetadata
     *
     * @return \Ibexa\Workflow\WorkflowTimeline\Value\WorkflowTimeline
     */
    public function create(WorkflowMetadata $workflowMetadata): WorkflowTimeline
    {
        $event = new TimelineEvent($workflowMetadata);
        $this->eventDispatcher->dispatch($event, TimelineEvents::COLLECT_ENTRIES);

        $entries = $this->sortEntries($event->getEntries());

        return new WorkflowTimeline($workflowMetadata, $entries);
    }

    /**
     * @param \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry[] $entries
     *
     * @return \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry[]
     */
    private function sortEntries(array $entries): array
    {
        $dates = array_map(static function (AbstractEntry $entry): DateTimeInterface {
            return $entry->getDate();
        }, $entries);

        array_multisort($entries, SORT_DESC, $dates);

        return $entries;
    }
}

class_alias(WorkflowTimelineFactory::class, 'EzSystems\EzPlatformWorkflow\WorkflowTimeline\WorkflowTimelineFactory');
