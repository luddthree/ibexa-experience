<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

use Ibexa\Workflow\Exception\NotFoundException;

class WorkflowDefinitionMetadata
{
    /** @var string */
    protected $name;

    /** @var string|null */
    protected $initialStage;

    /** @var \Ibexa\Workflow\Value\WorkflowStageDefinitionMetadata[] */
    protected $stagesMetadata;

    /** @var \Ibexa\Workflow\Value\WorkflowTransitionDefinitionMetadata[] */
    protected $transitionsMetadata;

    /** @var \Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata[] */
    protected $matchersMetadata;

    public function __construct(
        string $name,
        ?string $initialStage,
        array $stagesMetadata,
        array $transitionsMetadata,
        array $matchersMetadata
    ) {
        $this->name = $name;
        $this->initialStage = $initialStage;
        $this->stagesMetadata = $stagesMetadata;
        $this->transitionsMetadata = $transitionsMetadata;
        $this->matchersMetadata = $matchersMetadata;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getInitialStage(): ?string
    {
        return $this->initialStage;
    }

    public function setInitialStage(?string $initialStage): void
    {
        $this->initialStage = $initialStage;
    }

    /**
     * @return \Ibexa\Workflow\Value\WorkflowStageDefinitionMetadata[]
     */
    public function getStagesMetadata(): array
    {
        return $this->stagesMetadata;
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowStageDefinitionMetadata[] $stagesMetadata
     */
    public function setStagesMetadata(array $stagesMetadata): void
    {
        $this->stagesMetadata = $stagesMetadata;
    }

    /**
     * @return \Ibexa\Workflow\Value\WorkflowTransitionDefinitionMetadata[]
     */
    public function getTransitionsMetadata(): array
    {
        return $this->transitionsMetadata;
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowTransitionDefinitionMetadata[] $transitionsMetadata
     */
    public function setTransitionsMetadata(array $transitionsMetadata): void
    {
        $this->transitionsMetadata = $transitionsMetadata;
    }

    /**
     * @return \Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata[]
     */
    public function getMatchersMetadata(): array
    {
        return $this->matchersMetadata;
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata[] $matchersMetadata
     */
    public function setMatchersMetadata(array $matchersMetadata): void
    {
        $this->matchersMetadata = $matchersMetadata;
    }

    /**
     * @param string $matcherIdentifier
     *
     * @return \Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function getMatcherMetadata(string $matcherIdentifier): WorkflowMatcherDefinitionMetadata
    {
        if ($this->hasMatcherMetadata($matcherIdentifier)) {
            return $this->matchersMetadata[$matcherIdentifier];
        }

        throw new NotFoundException('Matcher Metadata', $matcherIdentifier);
    }

    /**
     * @param string $transitionIdentifier
     *
     * @return \Ibexa\Workflow\Value\WorkflowTransitionDefinitionMetadata
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function getTransitionMetadata(string $transitionIdentifier): WorkflowTransitionDefinitionMetadata
    {
        if ($this->hasTransitionMetadata($transitionIdentifier)) {
            return $this->transitionsMetadata[$transitionIdentifier];
        }

        throw new NotFoundException('Transition Metadata', $transitionIdentifier);
    }

    /**
     * @param string $stageIdentifier
     *
     * @return \Ibexa\Workflow\Value\WorkflowStageDefinitionMetadata
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function getStageMetadata(string $stageIdentifier): WorkflowStageDefinitionMetadata
    {
        if ($this->hasStageMetadata($stageIdentifier)) {
            return $this->stagesMetadata[$stageIdentifier];
        }

        throw new NotFoundException('Stage Metadata', $stageIdentifier);
    }

    /**
     * @param string $matcherIdentifier
     *
     * @return bool
     */
    public function hasMatcherMetadata(string $matcherIdentifier): bool
    {
        return isset($this->matchersMetadata[$matcherIdentifier]);
    }

    /**
     * @param string $transitionIdentifier
     *
     * @return bool
     */
    public function hasTransitionMetadata(string $transitionIdentifier): bool
    {
        return isset($this->transitionsMetadata[$transitionIdentifier]);
    }

    /**
     * @param string $stageIdentifier
     *
     * @return bool
     */
    public function hasStageMetadata(string $stageIdentifier): bool
    {
        return isset($this->stagesMetadata[$stageIdentifier]);
    }
}

class_alias(WorkflowDefinitionMetadata::class, 'EzSystems\EzPlatformWorkflow\Value\WorkflowDefinitionMetadata');
