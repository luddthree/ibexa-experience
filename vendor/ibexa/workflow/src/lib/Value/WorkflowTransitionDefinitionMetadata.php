<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

class WorkflowTransitionDefinitionMetadata
{
    /** @var string|null */
    protected $icon;

    /** @var string|null */
    private $color;

    /** @var string */
    protected $label;

    /** @var bool */
    protected $validate;

    /** @var \Ibexa\Workflow\Value\WorkflowTransitionNotificationDefinitionMetadata */
    private $notificationDefinitionMetadata;

    /** @var \Ibexa\Workflow\Value\WorkflowTransitionReviewersDefinitionMetadata */
    private $reviewersDefinitionMetadata;

    public function __construct(
        WorkflowTransitionNotificationDefinitionMetadata $notificationDefinitionMetadata,
        WorkflowTransitionReviewersDefinitionMetadata $reviewersDefinitionMetadata,
        string $label,
        bool $validate,
        ?string $icon = null,
        ?string $color = null
    ) {
        $this->setNotificationMetadata($notificationDefinitionMetadata);
        $this->setReviewersMetadata($reviewersDefinitionMetadata);
        $this->setLabel($label);
        $this->setValidate($validate);
        $this->setIcon($icon);
        $this->setColor($color);
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function hasColor(): bool
    {
        return $this->color !== null;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getValidate(): bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): void
    {
        $this->validate = $validate;
    }

    public function getNotificationMetadata(): WorkflowTransitionNotificationDefinitionMetadata
    {
        return $this->notificationDefinitionMetadata;
    }

    public function setNotificationMetadata(WorkflowTransitionNotificationDefinitionMetadata $notificationDefinitionMetadata): void
    {
        $this->notificationDefinitionMetadata = $notificationDefinitionMetadata;
    }

    public function getReviewersMetadata(): WorkflowTransitionReviewersDefinitionMetadata
    {
        return $this->reviewersDefinitionMetadata;
    }

    public function setReviewersMetadata(WorkflowTransitionReviewersDefinitionMetadata $reviewersDefinitionMetadata): void
    {
        $this->reviewersDefinitionMetadata = $reviewersDefinitionMetadata;
    }
}

class_alias(WorkflowTransitionDefinitionMetadata::class, 'EzSystems\EzPlatformWorkflow\Value\WorkflowTransitionDefinitionMetadata');
