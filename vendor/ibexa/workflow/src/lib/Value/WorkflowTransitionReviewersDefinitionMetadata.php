<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

class WorkflowTransitionReviewersDefinitionMetadata
{
    /** @var bool */
    protected $required;

    /** @var int|null */
    protected $userGroup;

    public function __construct(bool $required, ?int $userGroup = null)
    {
        $this->required = $required;
        $this->userGroup = $userGroup;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    public function getUserGroup(): ?int
    {
        return $this->userGroup;
    }

    public function setUserGroup(?int $userGroup = null): void
    {
        $this->userGroup = $userGroup;
    }
}

class_alias(WorkflowTransitionReviewersDefinitionMetadata::class, 'EzSystems\EzPlatformWorkflow\Value\WorkflowTransitionReviewersDefinitionMetadata');
