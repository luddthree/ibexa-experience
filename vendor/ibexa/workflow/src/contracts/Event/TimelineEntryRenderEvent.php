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
use Twig\TemplateWrapper;

class TimelineEntryRenderEvent extends Event
{
    /** @var \Ibexa\Workflow\Value\WorkflowMetadata */
    private $workflowMetadata;

    /** @var \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry */
    private $entry;

    /** @var \Twig\TemplateWrapper */
    private $template;

    /** @var string */
    private $blockName;

    /** @var array */
    private $parameters;

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata $workflowMetadata
     * @param \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry $entry
     * @param \Twig\TemplateWrapper $template
     * @param string $blockName
     * @param array $parameters
     */
    public function __construct(
        WorkflowMetadata $workflowMetadata,
        AbstractEntry $entry,
        TemplateWrapper $template,
        string $blockName,
        array $parameters = []
    ) {
        $this->workflowMetadata = $workflowMetadata;
        $this->entry = $entry;
        $this->template = $template;
        $this->blockName = $blockName;
        $this->parameters = $parameters;
    }

    /**
     * @return \Ibexa\Workflow\Value\WorkflowMetadata
     */
    public function getWorkflowMetadata(): WorkflowMetadata
    {
        return $this->workflowMetadata;
    }

    /**
     * @return \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry
     */
    public function getEntry(): AbstractEntry
    {
        return $this->entry;
    }

    /**
     * @return \Twig\TemplateWrapper
     */
    public function getTemplate(): TemplateWrapper
    {
        return $this->template;
    }

    /**
     * @param \Twig\TemplateWrapper $template
     */
    public function setTemplate(TemplateWrapper $template): void
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getBlockName(): string
    {
        return $this->blockName;
    }

    /**
     * @param string $blockName
     */
    public function setBlockName(string $blockName): void
    {
        $this->blockName = $blockName;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}

class_alias(TimelineEntryRenderEvent::class, 'EzSystems\EzPlatformWorkflow\Event\TimelineEntryRenderEvent');
