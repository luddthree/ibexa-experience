<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Event;

use Ibexa\Workflow\Value\Persistence\TransitionMetadata;
use Ibexa\Workflow\Value\WorkflowMetadata;
use Symfony\Contracts\EventDispatcher\Event;

class StageChangeEvent extends Event
{
    /** @var \Ibexa\Workflow\Value\WorkflowMetadata */
    private $workflowMetadata;

    /** @var \Ibexa\Workflow\Value\Persistence\TransitionMetadata */
    private $transitionMetadata;

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata $workflowMetadata
     * @param \Ibexa\Workflow\Value\Persistence\TransitionMetadata $transitionMetadata
     */
    public function __construct(
        WorkflowMetadata $workflowMetadata,
        TransitionMetadata $transitionMetadata
    ) {
        $this->workflowMetadata = $workflowMetadata;
        $this->transitionMetadata = $transitionMetadata;
    }

    /**
     * @return \Ibexa\Workflow\Value\WorkflowMetadata
     */
    public function getWorkflowMetadata(): WorkflowMetadata
    {
        return $this->workflowMetadata;
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata $workflowMetadata
     */
    public function setWorkflowMetadata(WorkflowMetadata $workflowMetadata): void
    {
        $this->workflowMetadata = $workflowMetadata;
    }

    /**
     * @return \Ibexa\Workflow\Value\Persistence\TransitionMetadata
     */
    public function getTransitionMetadata(): TransitionMetadata
    {
        return $this->transitionMetadata;
    }

    /**
     * @param \Ibexa\Workflow\Value\Persistence\TransitionMetadata $transitionMetadata
     */
    public function setTransitionMetadata(TransitionMetadata $transitionMetadata): void
    {
        $this->transitionMetadata = $transitionMetadata;
    }
}

class_alias(StageChangeEvent::class, 'EzSystems\EzPlatformWorkflow\Event\StageChangeEvent');
