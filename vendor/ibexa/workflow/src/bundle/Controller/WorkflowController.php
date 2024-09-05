<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Workflow\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\NotificationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalNot;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Workflow\Notification\Factory;
use Ibexa\Workflow\Pagerfanta\Adapter\WorkflowMetadataAdapter;
use Ibexa\Workflow\Search\Criterion\ContentIsInTrash;
use Ibexa\Workflow\Search\Criterion\ContentIsOnSpecificStage;
use Ibexa\Workflow\Value\WorkflowMetadataQuery;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkflowController extends Controller
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowDefinitionMetadataRegistry;

    /** @var \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface */
    private $workflowService;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\NotificationService */
    private $notificationService;

    /** @var \Ibexa\Workflow\Notification\Factory */
    private $notificationFactory;

    /** @var \Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /**
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry
     * @param \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface $workflowService
     * @param \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $configResolver
     */
    public function __construct(
        WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry,
        WorkflowServiceInterface $workflowService,
        ContentService $contentService,
        NotificationService $notificationService,
        ConfigResolverInterface $configResolver,
        Factory $notificationFactory,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->workflowDefinitionMetadataRegistry = $workflowDefinitionMetadataRegistry;
        $this->workflowService = $workflowService;
        $this->configResolver = $configResolver;
        $this->contentService = $contentService;
        $this->notificationService = $notificationService;
        $this->notificationFactory = $notificationFactory;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request): Response
    {
        $page = $request->query->get('page') ?? 1;

        $allWorkflowMetadata = $this->workflowDefinitionMetadataRegistry->getAllWorkflowMetadata();

        $pagerfanta = new Pagerfanta(new ArrayAdapter($allWorkflowMetadata));
        $pagerfanta->setMaxPerPage(
            (int)$this->configResolver->getParameter('workflows_config.pagination.workflow_limit')
        );
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        return $this->render('@ibexadesign/ibexa_workflow/admin/workflow/list.html.twig', [
            'pager' => $pagerfanta,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $workflowName
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, string $workflowName): Response
    {
        $pageMap = $request->query->get('page', []);

        $workflowDefinitionMetadata = $this->workflowDefinitionMetadataRegistry->getWorkflowMetadata(
            $workflowName
        );

        $stagesMetadata = $workflowDefinitionMetadata->getStagesMetadata();
        $stageNames = array_keys($stagesMetadata);

        $list = [];
        foreach ($stageNames as $stageName) {
            $page = $pageMap[$stageName] ?? 1;

            $query = new WorkflowMetadataQuery();
            $query->filter = new LogicalAnd([
                new ContentIsOnSpecificStage($workflowName, $stageName),
                new LogicalNot(new ContentIsInTrash()),
            ]);

            $pagerfanta = new Pagerfanta(new WorkflowMetadataAdapter($this->workflowService, $query));
            $pagerfanta->setMaxPerPage(
                (int)$this->configResolver->getParameter(
                    'workflows_config.pagination.workflow_limit'
                )
            );
            $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

            $list[$stageName] = $pagerfanta;
        }

        $workflowDefinition = $this->getWorkflowDefinition($workflowName);

        return $this->render('@ibexadesign/ibexa_workflow/admin/workflow/view.html.twig', [
            'workflow' => $workflowDefinitionMetadata,
            'stages_metadata' => $stagesMetadata,
            'content_by_stage_pagination_list' => $list,
            'workflow_definition' => $workflowDefinition,
        ]);
    }

    public function unlockVersionAction(int $contentId, int $versionNo): Response
    {
        $content = $this->contentService->loadContent($contentId, null, $versionNo);
        $this->workflowService->unlockVersion($content->versionInfo);

        if ($content->contentInfo->isDraft()) {
            return $this->redirectToRoute('ibexa.dashboard');
        } else {
            return $this->redirectToRoute('ibexa.content.view', [
                'contentId' => $content->contentInfo->id,
            ]);
        }
    }

    public function askUnlockVersionAction(int $contentId, int $versionNo, int $userId): Response
    {
        $content = $this->contentService->loadContent($contentId, null, $versionNo);

        if ($this->workflowService->isVersionLocked($content->versionInfo, $userId)) {
            $versionLock = $this->workflowService->getVersionLock($content->versionInfo);

            $this->notificationService->createNotification(
                $this->notificationFactory->getAskUnlockNotificationCreateStruct($versionLock)
            );
        }

        $this->notificationHandler->success(
            /** @Desc("Request access has been sent") */
            'draft.unlock.ask.notification',
            [],
            'ibexa_workflow'
        );

        if ($content->contentInfo->isDraft()) {
            return $this->redirectToRoute('ibexa.dashboard');
        } else {
            return $this->redirectToRoute('ibexa.content.view', [
                'contentId' => $content->contentInfo->id,
            ]);
        }
    }

    /**
     * Get Workflow definition from the list set by SiteAccess-aware semantic configuration.
     *
     * @param string $workflowName
     *
     * @return array
     */
    private function getWorkflowDefinition(string $workflowName): array
    {
        $workflows = $this->configResolver->getParameter('workflows');

        return $workflows[$workflowName] ?? [];
    }
}

class_alias(WorkflowController::class, 'EzSystems\EzPlatformWorkflowBundle\Controller\WorkflowController');
