<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Tab;

use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Workflow\Builder\CreatorsMapBuilder;
use Ibexa\Workflow\Builder\StagesMapBuilder;
use Ibexa\Workflow\Dashboard\Block\ListFilter;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class MyDraftsUnderReviewTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    public const PAGINATION_PARAM_NAME = 'my_drafts_under_review_page';

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface */
    private $workflowService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Workflow\Builder\StagesMapBuilder */
    private $stagesMapBuilder;

    /** @var \Ibexa\Workflow\Builder\CreatorsMapBuilder */
    private $creatorsMapBuilder;

    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowDefinitionMetadataRegistry;

    /** @var \Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface */
    private $supportStrategy;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        WorkflowServiceInterface $workflowService,
        PermissionResolver $permissionResolver,
        StagesMapBuilder $stagesMapBuilder,
        CreatorsMapBuilder $creatorsMapBuilder,
        WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry,
        WorkflowSupportStrategyInterface $supportStrategy,
        ConfigResolverInterface $configResolver,
        UserService $userService
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->requestStack = $requestStack;
        $this->workflowService = $workflowService;
        $this->permissionResolver = $permissionResolver;
        $this->stagesMapBuilder = $stagesMapBuilder;
        $this->creatorsMapBuilder = $creatorsMapBuilder;
        $this->workflowDefinitionMetadataRegistry = $workflowDefinitionMetadataRegistry;
        $this->supportStrategy = $supportStrategy;
        $this->configResolver = $configResolver;
        $this->userService = $userService;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return 'my-drafts-under-review';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return /** @Desc("Drafts to review") */
            $this->translator->trans('tab.name.my_drafts_under_review', [], 'ibexa_workflow');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder(): int
    {
        return 400;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(): string
    {
        return '@ibexadesign/ibexa_workflow/admin/dashboard/tab/my_drafts_under_review/tab.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        $pages = $this->requestStack->getCurrentRequest()->query->get('page', []);
        $currentPage = $pages[self::PAGINATION_PARAM_NAME] ?? 1;

        $workflowsMetadata = $this->workflowService->loadOngoingWorkflowMetadataOriginatedByUser(
            $this->permissionResolver->getCurrentUserReference(),
            null,
            -1
        );

        $workflowsMetadata = $this->filterWorkflowList($workflowsMetadata);

        $pagination = new Pagerfanta(new ArrayAdapter($workflowsMetadata));
        $pagination->setMaxPerPage($this->configResolver->getParameter('pagination.content_draft_limit'));
        $pagination->setCurrentPage(min(max($currentPage, 1), $pagination->getNbPages()));

        $stagesMap = $this->stagesMapBuilder->buildStagesMapByWorkflow($pagination->getCurrentPageResults());
        $creatorsMap = $this->creatorsMapBuilder->buildCreatorsMapForWorkflowMetadataList($pagination->getCurrentPageResults());

        return array_replace($contextParameters, [
            'data' => $pagination->getCurrentPageResults(),
            'stages_map' => $stagesMap,
            'creators_map' => $creatorsMap,
            'workflow_versions_details' => $this->buildWorkflowMetadataVersionDetails($workflowsMetadata),
            'workflow_definition_metadata' => $this->workflowDefinitionMetadataRegistry->getAllWorkflowMetadata(),
            'pager' => $pagination,
            'pager_options' => ($contextParameters['pager_options'] ?? []) + [
                'pageParameter' => sprintf('[page][%s]', self::PAGINATION_PARAM_NAME),
                'routeParams' => [
                    '_fragment' => 'ibexa-tab-dashboard-my-my-drafts-under-review',
                ],
            ],
        ]);
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata[] $workflowMetadataList
     *
     * @return \Ibexa\Workflow\Value\WorkflowMetadata[]
     */
    private function filterWorkflowList(array $workflowMetadataList): array
    {
        $filter = new ListFilter\ChainListFilter([
            new ListFilter\SupportedWorkflowsOnlyListFilter($this->supportStrategy),
        ]);

        return $filter->filter($workflowMetadataList);
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata[] $workflowsMetadata
     */
    private function buildWorkflowMetadataVersionDetails(array $workflowsMetadata): array
    {
        $details = [];

        foreach ($workflowsMetadata as $workflowMetadata) {
            $versionInfo = $workflowMetadata->versionInfo;
            $versionDetails = [
                'can_edit' => $this->permissionResolver->canUser('content', 'edit', $versionInfo),
                'can_unlock' => $this->permissionResolver->canUser('content', 'unlock', $versionInfo),
                'lock' => false,
                'assignee' => false,
            ];

            try {
                $versionLock = $this->workflowService->getVersionLock($versionInfo);

                $versionDetails['lock'] = $versionLock;
                $versionDetails['assignee'] = $this->userService->loadUser($versionLock->userId);
            } catch (NotFoundException $exception) {
                // Do nothing
            }

            $details[$versionInfo->id] = $versionDetails;
        }

        return $details;
    }
}

class_alias(MyDraftsUnderReviewTab::class, 'EzSystems\EzPlatformWorkflow\Tab\MyDraftsUnderReviewTab');
