<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\Contracts\Workflow\Event\TimelineEntryRenderEvent;
use Ibexa\Contracts\Workflow\Event\TimelineEvent;
use Ibexa\Contracts\Workflow\Event\TimelineEvents;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\Workflow\Value\WorkflowDefinitionMetadata;
use Ibexa\Workflow\WorkflowTimeline\Value\TransitionEntry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Transition;

class TimelineTransitionEntriesSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowDefinitionMetadataRegistry;

    /**
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry
     */
    public function __construct(
        WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry
    ) {
        $this->workflowDefinitionMetadataRegistry = $workflowDefinitionMetadataRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TimelineEvents::COLLECT_ENTRIES => ['onEntriesCollect'],
            TimelineEvents::ENTRY_RENDER => ['onEntryRender'],
        ];
    }

    /**
     * @param \Ibexa\Contracts\Workflow\Event\TimelineEvent $event
     */
    public function onEntriesCollect(TimelineEvent $event): void
    {
        $workflowMetadata = $event->getWorkflowMetadata();

        foreach ($workflowMetadata->transitions as $transitionMetadata) {
            $event->addEntry(
                new TransitionEntry(
                    $workflowMetadata,
                    $transitionMetadata
                )
            );
        }
    }

    /**
     * @param \Ibexa\Contracts\Workflow\Event\TimelineEntryRenderEvent $event
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    public function onEntryRender(TimelineEntryRenderEvent $event): void
    {
        /** @var \Ibexa\Workflow\WorkflowTimeline\Value\TransitionEntry $entry */
        $entry = $event->getEntry();

        if ($entry->getIdentifier() !== 'transition') {
            return;
        }

        $parameters = $event->getParameters();
        $workflowMetadata = $entry->getWorkflowMetadata();
        $definition = $workflowMetadata->workflow->getDefinition();

        $transitions = $definition->getTransitions();
        $transition = $this->findTransition($transitions, $entry->getTransitionMetadata()->name);

        $workflowDefinitionMetadata = $this->workflowDefinitionMetadataRegistry->getWorkflowMetadata($workflowMetadata->name);
        $transitionDefinitionMetadata = $workflowDefinitionMetadata->getTransitionMetadata($entry->getTransitionMetadata()->name);
        $stagesDefinitionMetadataList = $this->getStageDefinitionMetadataList($transition, $workflowDefinitionMetadata);
        $stageNames = $this->getStageNames($stagesDefinitionMetadataList);

        $parameters['workflow_definition_metadata'] = $workflowDefinitionMetadata;
        $parameters['transition_definition_metadata'] = $transitionDefinitionMetadata;
        $parameters['transition_metadata'] = $entry->getTransitionMetadata();
        $parameters['stages_definition_metadata_list'] = $stagesDefinitionMetadataList;
        $parameters['stage_names'] = $stageNames;

        $event->setParameters($parameters);
    }

    /**
     * @param \Symfony\Component\Workflow\Transition[] $transitions
     * @param string $name
     *
     * @return \Symfony\Component\Workflow\Transition
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    private function findTransition(array $transitions, string $name): Transition
    {
        foreach ($transitions as $transition) {
            if ($transition->getName() === $name) {
                return $transition;
            }
        }

        throw new BadStateException('$transitions', "Could not find transition '{$name}'.");
    }

    /**
     * @param \Symfony\Component\Workflow\Transition $transition
     * @param \Ibexa\Workflow\Value\WorkflowDefinitionMetadata $workflowDefinitionMetadata
     *
     * @return \Ibexa\Workflow\Value\WorkflowStageDefinitionMetadata[]
     */
    private function getStageDefinitionMetadataList(
        Transition $transition,
        WorkflowDefinitionMetadata $workflowDefinitionMetadata
    ): array {
        return array_combine(
            $transition->getTos(),
            array_map([$workflowDefinitionMetadata, 'getStageMetadata'], $transition->getTos())
        );
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowStageDefinitionMetadata[] $stagesDefinitionMetadataList
     *
     * @return string[]
     */
    private function getStageNames(array $stagesDefinitionMetadataList): array
    {
        $stageNames = [];
        foreach ($stagesDefinitionMetadataList as $key => $stageDefinitionMetadata) {
            $stageNames[$key] = $stageDefinitionMetadata->getLabel();
        }

        return $stageNames;
    }
}

class_alias(TimelineTransitionEntriesSubscriber::class, 'EzSystems\EzPlatformWorkflow\Event\Subscriber\TimelineTransitionEntriesSubscriber');
