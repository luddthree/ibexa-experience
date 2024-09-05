<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Renderer;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Workflow\Event\TimelineEntryRenderEvent;
use Ibexa\Contracts\Workflow\Event\TimelineEvents;
use Ibexa\Workflow\Exception\MissingWorkflowTimelineEntryBlockException;
use Ibexa\Workflow\Exception\ValueMapperNotFoundException;
use Ibexa\Workflow\Value\WorkflowMetadata;
use Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig\TemplateWrapper;

class WorkflowTimelineEntryRenderer implements WorkflowTimelineEntryRendererInterface
{
    public const ENTRY_BLOCK_NAME = 'ez_workflow_timeline_entry_%s';
    public const ENTRY_BLOCK_NAME_FALLBACK = 'ez_workflow_timeline_entry_fallback';

    /** @var \Twig\Environment */
    private $twig;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        Environment $twig,
        EventDispatcherInterface $eventDispatcher,
        ConfigResolverInterface $configResolver
    ) {
        $this->twig = $twig;
        $this->eventDispatcher = $eventDispatcher;
        $this->configResolver = $configResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function render(
        WorkflowMetadata $workflowMetadata,
        AbstractEntry $entry,
        array $parameters = []
    ): string {
        try {
            $blockName = $this->getValueBlockName($entry->getIdentifier());
        } catch (ValueMapperNotFoundException $exception) {
            $blockName = self::ENTRY_BLOCK_NAME_FALLBACK;
        }

        $localTemplate = null;
        if (isset($parameters['template'])) {
            $localTemplate = $parameters['template'];
            unset($parameters['template']);
        }

        $template = $this->findTemplateWithBlock($blockName, $localTemplate);
        if ($template === null) {
            throw new MissingWorkflowTimelineEntryBlockException(
                "Could not find block for {$entry->getIdentifier()}: {$blockName}."
            );
        }

        $event = new TimelineEntryRenderEvent($workflowMetadata, $entry, $template, $blockName, $parameters);
        $this->eventDispatcher->dispatch($event, TimelineEvents::ENTRY_RENDER);

        return $event->getTemplate()->renderBlock($event->getBlockName(), $event->getParameters());
    }

    /**
     * Generates value block name based on matcher identifier.
     *
     * @param string $identifier
     *
     * @return string
     */
    protected function getValueBlockName(string $identifier): string
    {
        return sprintf(self::ENTRY_BLOCK_NAME, strtolower($identifier));
    }

    /**
     * Find the first template containing block definition $blockName.
     *
     * @param string $blockName
     * @param string|\Twig\Template $localTemplate
     *
     * @return \Twig\TemplateWrapper|string|null
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function findTemplateWithBlock(string $blockName, $localTemplate = null): ?TemplateWrapper
    {
        if ($localTemplate !== null) {
            if (\is_string($localTemplate)) {
                $localTemplate = $this->twig->load($localTemplate);
            }

            if ($localTemplate->hasBlock($blockName)) {
                return $localTemplate;
            }
        }

        foreach ($this->getResources() as &$template) {
            if (\is_string($template)) {
                // Load the template if it is necessary
                $template = $this->twig->load($template);
            }

            if ($template->hasBlock($blockName)) {
                return $template;
            }
        }

        return null;
    }

    /**
     * @return string[]
     */
    protected function getResources(): array
    {
        $resources = $this->configResolver->getParameter('workflows_config.timeline_entry_templates');
        usort($resources, static function ($a, $b) {
            return $b['priority'] - $a['priority'];
        });

        return array_column($resources, 'template');
    }
}

class_alias(WorkflowTimelineEntryRenderer::class, 'EzSystems\EzPlatformWorkflow\Renderer\WorkflowTimelineEntryRenderer');
