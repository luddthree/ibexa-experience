<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\AdminUi\Form\Data\Version\VersionRemoveData;
use Ibexa\AdminUi\Form\Factory\FormFactory;
use Ibexa\AdminUi\Tab\Event\TabEvents;
use Ibexa\AdminUi\Tab\Event\TabViewRenderEvent;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Workflow\Exception\NotFoundException;
use Ibexa\Workflow\Value\WorkflowDefinitionMetadata;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;

class UiComponentsSubscriber implements EventSubscriberInterface
{
    public const PERMISSION_LAYER_TRANSITION_BLOCKER_CODE = '23373c81-5524-49a0-bf5a-b462748aa9e2';

    /** @var \Symfony\Component\Workflow\Registry */
    private $workflowRegistry;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\AdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface */
    private $workflowService;

    /** @var \Ibexa\Workflow\Factory\Registry\WorkflowDefinitionMetadataRegistryFactory */
    private $workflowMetadataRegistry;

    /**
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface $workflowRegistry
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     * @param \Ibexa\AdminUi\Form\Factory\FormFactory $formFactory
     * @param \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface $workflowService
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface $workflowMetadataRegistry
     */
    public function __construct(
        WorkflowRegistryInterface $workflowRegistry,
        ContentService $contentService,
        FormFactory $formFactory,
        WorkflowServiceInterface $workflowService,
        WorkflowDefinitionMetadataRegistryInterface $workflowMetadataRegistry
    ) {
        $this->workflowRegistry = $workflowRegistry;
        $this->contentService = $contentService;
        $this->formFactory = $formFactory;
        $this->workflowService = $workflowService;
        $this->workflowMetadataRegistry = $workflowMetadataRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TabEvents::TAB_RENDER => ['onTabRender', 0],
        ];
    }

    /**
     * @param \Ibexa\AdminUi\Tab\Event\TabViewRenderEvent $event
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onTabRender(TabViewRenderEvent $event): void
    {
        if ($event->getTabIdentifier() !== 'versions') {
            return;
        }

        $parameters = $event->getParameters();

        if (!isset($parameters['draft_pager'])) {
            return;
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $parameters['content'];

        /** @var \Ibexa\AdminUi\UI\Dataset\VersionsDataset $dataset */
        $dataset = $parameters['versions_dataset'];

        // Create workflows map
        $draftVersions = $dataset->getDraftVersions();
        $workflowDefinitionMetadata = [];
        $stages = [];
        foreach ($draftVersions as $versionInfo) {
            $contentVersion = $this->contentService->loadContentByVersionInfo($versionInfo);
            $workflows = $this->workflowRegistry->getSupportedWorkflows($contentVersion);

            if (!array_key_exists($versionInfo->versionNo, $stages)) {
                $stages[$versionInfo->versionNo] = [];
            }

            foreach ($workflows as $workflow) {
                try {
                    $workflowMetadata = $this->workflowService->loadWorkflowMetadataForContent($contentVersion);
                } catch (NotFoundException $e) {
                    continue;
                }

                $workflowName = $workflow->getName();
                if (!array_key_exists($workflowName, $stages)) {
                    $stages[$versionInfo->versionNo][$workflowName] = [];
                }

                if (!array_key_exists($workflowName, $workflowDefinitionMetadata)) {
                    $workflowDefinitionMetadata[$workflowName] = $this->workflowMetadataRegistry->getWorkflowMetadata($workflowName);
                }

                $stages[$versionInfo->versionNo][$workflowName] = [];
                $places = array_keys($workflow->getMarking($contentVersion)->getPlaces());
                foreach ($places as $place) {
                    $stages[$versionInfo->versionNo][$workflowName][] = $this->createStagePropertiesArray(
                        $workflowDefinitionMetadata[$workflowName],
                        $place
                    );
                }
            }
        }

        // ignore changes to the table if there are no workflows
        if (empty(array_filter($stages))) {
            return;
        }

        // Assign parameters
        $parameters['workflow_definition_metadata'] = $workflowDefinitionMetadata;
        $parameters['workflow_stages_map'] = $stages;

        $draftPagerfanta = new Pagerfanta(new ArrayAdapter($draftVersions));
        $draftPaginationParams = $parameters['draft_pagination_params'];
        $draftPagerfanta->setMaxPerPage($draftPaginationParams['limit']);
        $draftPagerfanta->setCurrentPage(min($draftPaginationParams['page'], $draftPagerfanta->getNbPages()));

        $parameters['draft_pager'] = $draftPagerfanta;

        // Inject to original tab
        $event->setParameters($parameters);
        $event->setTemplate('@ibexadesign/ibexa_workflow/admin/content_view/tab/versions/tab.html.twig');
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowDefinitionMetadata $workflowDefinitionMetadata
     * @param string $stage
     *
     * @return array
     */
    private function createStagePropertiesArray(
        WorkflowDefinitionMetadata $workflowDefinitionMetadata,
        string $stage
    ): array {
        $stageMetadata = $workflowDefinitionMetadata->getStagesMetadata()[$stage];

        return [
            'identifier' => $stage,
            'label' => $stageMetadata->getLabel(),
            'color' => $stageMetadata->getColor(),
        ];
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo[] $versions
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createVersionRemoveForm(ContentInfo $contentInfo, array $versions): FormInterface
    {
        $versionNumbers = array_column($versions, 'versionNo');
        $versionsData = array_combine($versionNumbers, array_fill_keys($versionNumbers, false));

        return $this->formFactory->removeVersion(new VersionRemoveData($contentInfo, $versionsData));
    }
}

class_alias(UiComponentsSubscriber::class, 'EzSystems\EzPlatformWorkflow\Event\Subscriber\UiComponentsSubscriber');
