<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Event;

use Ibexa\Workflow\Value\WorkflowMetadata;
use Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry;
use Symfony\Contracts\EventDispatcher\Event;

class TimelineEvent extends Event
{
    /** @var \Ibexa\Workflow\Value\WorkflowMetadata */
    private $workflowMetadata;

    /** @var \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry[] */
    private $entries;

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata $workflowMetadata
     * @param \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry[] $entries
     */
    public function __construct(
        WorkflowMetadata $workflowMetadata,
        array $entries = []
    ) {
        $this->workflowMetadata = $workflowMetadata;
        $this->entries = $entries;
    }

    /**
     * @return \Ibexa\Workflow\Value\WorkflowMetadata
     */
    public function getWorkflowMetadata(): WorkflowMetadata
    {
        return $this->workflowMetadata;
    }

    /**
     * @return \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @param \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry[] $entries
     */
    public function setEntries(array $entries): void
    {
        $this->entries = $entries;
    }

    /**
     * @param \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry $entry
     */
    public function addEntry(AbstractEntry $entry): void
    {
        $this->entries[] = $entry;
    }

    /**
     * @param \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry[] $entries
     */
    public function addEntries(array $entries): void
    {
        $this->entries = array_merge($this->entries, $entries);
    }
}

class_alias(TimelineEvent::class, 'EzSystems\EzPlatformWorkflow\Event\TimelineEvent');
