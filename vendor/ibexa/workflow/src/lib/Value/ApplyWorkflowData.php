<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Workflow\Value;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

class ApplyWorkflowData
{
    /** @var string */
    private $workflowName;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    private $content;

    /** @var string */
    private $transition;

    /**
     * @param string $workflowName
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param string $transition
     */
    public function __construct(
        string $workflowName,
        Content $content,
        string $transition
    ) {
        $this->workflowName = $workflowName;
        $this->content = $content;
        $this->transition = $transition;
    }

    /**
     * @return string
     */
    public function getWorkflowName(): string
    {
        return $this->workflowName;
    }

    /**
     * @param string $workflowName
     */
    public function setWorkflowName(string $workflowName): void
    {
        $this->workflowName = $workflowName;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    public function getContent(): Content
    {
        return $this->content;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     */
    public function setContent(Content $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getTransition(): string
    {
        return $this->transition;
    }

    /**
     * @param string $transition
     */
    public function setTransition(string $transition): void
    {
        $this->transition = $transition;
    }
}

class_alias(ApplyWorkflowData::class, 'EzSystems\EzPlatformWorkflow\Value\ApplyWorkflowData');
