<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Workflow\Templating\Twig;

use Ibexa\Workflow\Renderer\WorkflowTimelineEntryRendererInterface;
use Ibexa\Workflow\Value\WorkflowMetadata;
use Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WorkflowTimelineEntryRenderingExtension extends AbstractExtension
{
    /** @var \Ibexa\Workflow\Renderer\WorkflowTimelineEntryRendererInterface */
    private $workflowTimelineEntryRenderer;

    /**
     * @param \Ibexa\Workflow\Renderer\WorkflowTimelineEntryRendererInterface $workflowTimelineEntryRenderer
     */
    public function __construct(WorkflowTimelineEntryRendererInterface $workflowTimelineEntryRenderer)
    {
        $this->workflowTimelineEntryRenderer = $workflowTimelineEntryRenderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ez_render_workflow_timeline_entry',
                [$this, 'renderEntry'],
                [
                    'is_safe' => ['html'],
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_render_workflow_timeline_entry',
                ]
            ),
            new TwigFunction(
                'ibexa_render_workflow_timeline_entry',
                [$this, 'renderEntry'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'ezplatform_workflow.timeline.entry.render';
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata $workflowMetadata
     * @param \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry $entry
     * @param array $params
     *
     * @return string
     */
    public function renderEntry(
        WorkflowMetadata $workflowMetadata,
        AbstractEntry $entry,
        array $params = []
    ): string {
        return $this->workflowTimelineEntryRenderer->render($workflowMetadata, $entry, $params);
    }
}

class_alias(WorkflowTimelineEntryRenderingExtension::class, 'EzSystems\EzPlatformWorkflowBundle\Templating\Twig\WorkflowTimelineEntryRenderingExtension');
