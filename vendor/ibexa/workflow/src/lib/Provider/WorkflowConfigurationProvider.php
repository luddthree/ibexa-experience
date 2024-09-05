<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Provider;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Workflow\Event\Action\ConditionalActionListenerInterface;
use Ibexa\Contracts\Workflow\Event\Action\WorkflowActionListenerInterface;
use Ibexa\Contracts\Workflow\Factory\WorkflowDefinitionMetadata\WorkflowDefinitionMetadataFactoryInterface;
use Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface;
use Ibexa\Contracts\Workflow\Provider\WorkflowDefinitionMetadataProviderInterface;
use Ibexa\Contracts\Workflow\Provider\WorkflowProviderInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowActionListenerRegistryInterface;
use Ibexa\Workflow\MarkingStore\WorkflowMarkingStore;
use SplObjectStorage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Metadata\InMemoryMetadataStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

class WorkflowConfigurationProvider implements WorkflowProviderInterface, WorkflowDefinitionMetadataProviderInterface
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface */
    private $workflowHandler;

    /** @var \Ibexa\Contracts\Workflow\Factory\WorkflowDefinitionMetadata\WorkflowDefinitionMetadataFactoryInterface */
    private $workflowDefinitionMetadataFactory;

    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowActionListenerRegistryInterface */
    private $actionListenerRegistry;

    /**
     * @param \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $configResolver
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface $workflowHandler
     * @param \Ibexa\Contracts\Workflow\Factory\WorkflowDefinitionMetadata\WorkflowDefinitionMetadataFactoryInterface $workflowDefinitionMetadataFactory
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        EventDispatcherInterface $eventDispatcher,
        HandlerInterface $workflowHandler,
        WorkflowDefinitionMetadataFactoryInterface $workflowDefinitionMetadataFactory,
        WorkflowActionListenerRegistryInterface $actionListenerRegistry
    ) {
        $this->configResolver = $configResolver;
        $this->eventDispatcher = $eventDispatcher;
        $this->workflowHandler = $workflowHandler;
        $this->workflowDefinitionMetadataFactory = $workflowDefinitionMetadataFactory;
        $this->actionListenerRegistry = $actionListenerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflows(): array
    {
        $configurations = $this->configResolver->getParameter('workflows');
        $workflows = [];

        foreach ($configurations as $workflowIdentifier => $configuration) {
            $stagesMetadata = [];
            $transitionsMetadata = new SplObjectStorage();

            $definitionBuilder = new DefinitionBuilder();

            foreach ($configuration['stages'] as $stageIdentifier => $stage) {
                $definitionBuilder->addPlace($stageIdentifier);
                $stagesMetadata[$stageIdentifier]['actions'] = $stage['actions'] ?? [];
            }

            foreach ($configuration['transitions'] as $transitionIdentifier => $transition) {
                $transitionDefinition = new Transition(
                    $transitionIdentifier,
                    $transition['from'],
                    $transition['to']
                );

                $definitionBuilder->addTransition($transitionDefinition);
                $transitionsMetadata[$transitionDefinition] = ['actions' => $transition['actions'] ?? []];
            }

            $definitionBuilder->setInitialPlaces([$configuration['initial_stage']]);
            $definitionBuilder->setMetadataStore(new InMemoryMetadataStore([], $stagesMetadata, $transitionsMetadata));

            $workflow = new Workflow(
                $definitionBuilder->build(),
                new WorkflowMarkingStore($workflowIdentifier, $this->workflowHandler),
                $this->eventDispatcher,
                $workflowIdentifier
            );

            $this->registerActionListeners($workflow);

            $workflows[$workflowIdentifier] = $workflow;
        }

        return $workflows;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowDefinitionsMetadata(): array
    {
        $configurations = $this->configResolver->getParameter('workflows');

        $workflowDefinitionsMetadata = [];
        foreach ($configurations as $identifier => $configuration) {
            $workflowDefinitionsMetadata[$identifier] = $this->workflowDefinitionMetadataFactory->createWorkflowDefinitionMetadata($configuration);
        }

        return $workflowDefinitionsMetadata;
    }

    protected function registerActionListeners(Workflow $workflow): void
    {
        $places = $workflow->getDefinition()->getPlaces();
        foreach ($places as $place) {
            $placeMetadata = $workflow->getMetadataStore()->getPlaceMetadata($place);

            if (!is_array($placeMetadata['actions'])) {
                continue;
            }

            foreach ($placeMetadata['actions'] as $actionIdentifier => $actionData) {
                $actionListener = $this->actionListenerRegistry->getActionListener($actionIdentifier);

                if (!$actionListener instanceof WorkflowActionListenerInterface) {
                    continue;
                }

                $eventName = sprintf('workflow.%s.entered.%s', $workflow->getName(), $place);
                $this->eventDispatcher->addListener(
                    $eventName,
                    [
                        $actionListener,
                        $actionListener instanceof ConditionalActionListenerInterface
                            ? 'onConditionalWorkflowEvent'
                            : 'onWorkflowEvent',
                    ]
                );
            }
        }

        $transitions = $workflow->getDefinition()->getTransitions();
        foreach ($transitions as $transition) {
            $transitionMetadata = $workflow->getMetadataStore()->getTransitionMetadata($transition);

            if (!is_array($transitionMetadata['actions'])) {
                continue;
            }

            foreach ($transitionMetadata['actions'] as $actionIdentifier => $actionData) {
                $actionListener = $this->actionListenerRegistry->getActionListener($actionIdentifier);

                if (!$actionListener instanceof WorkflowActionListenerInterface) {
                    continue;
                }

                $eventName = sprintf('workflow.%s.transition.%s', $workflow->getName(), $transition->getName());
                $this->eventDispatcher->addListener(
                    $eventName,
                    [
                        $actionListener,
                        $actionListener instanceof ConditionalActionListenerInterface
                            ? 'onConditionalWorkflowEvent'
                            : 'onWorkflowEvent',
                    ]
                );
            }
        }
    }
}

class_alias(WorkflowConfigurationProvider::class, 'EzSystems\EzPlatformWorkflow\Provider\WorkflowConfigurationProvider');
