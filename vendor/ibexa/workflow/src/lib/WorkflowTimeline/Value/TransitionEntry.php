<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\WorkflowTimeline\Value;

use Ibexa\Workflow\Value\TransitionMetadata;
use Ibexa\Workflow\Value\WorkflowMetadata;

class TransitionEntry extends AbstractEntry
{
    /** @var \Ibexa\Workflow\Value\WorkflowMetadata */
    private $workflowMetadata;

    /** @var \Ibexa\Workflow\Value\TransitionMetadata */
    private $transitionMetadata;

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata $workflowMetadata
     * @param \Ibexa\Workflow\Value\TransitionMetadata $transitionMetadata
     */
    public function __construct(
        WorkflowMetadata $workflowMetadata,
        TransitionMetadata $transitionMetadata
    ) {
        parent::__construct($transitionMetadata->date);

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
     * @return \Ibexa\Workflow\Value\TransitionMetadata
     */
    public function getTransitionMetadata(): TransitionMetadata
    {
        return $this->transitionMetadata;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'transition';
    }
}

class_alias(TransitionEntry::class, 'EzSystems\EzPlatformWorkflow\WorkflowTimeline\Value\TransitionEntry');
