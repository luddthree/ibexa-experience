<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

class WorkflowStageDefinitionMetadata
{
    /** @var string */
    protected $label;

    /** @var string */
    protected $color;

    /** @var bool */
    protected $isLastStage;

    public function __construct(string $label, ?string $color = null, bool $isLastStage = false)
    {
        $this->label = $label;
        $this->color = $color;
        $this->isLastStage = $isLastStage;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    public function isLastStage(): bool
    {
        return $this->isLastStage;
    }

    public function setIsLastStage(bool $isLastStage): void
    {
        $this->isLastStage = $isLastStage;
    }
}

class_alias(WorkflowStageDefinitionMetadata::class, 'EzSystems\EzPlatformWorkflow\Value\WorkflowStageDefinitionMetadata');
