<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Dashboard\Block;

use Ibexa\AdminUi\Component\TwigComponent;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Workflow\Builder\CreatorsMapBuilder;
use Ibexa\Workflow\Builder\StagesMapBuilder;
use Ibexa\Workflow\Dashboard\Block\ListFilter\ChainListFilter;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface;
use Twig\Environment;

class ReviewQueueBlock extends TwigComponent
{
    public const PAGINATION_PARAM_NAME = 'review_queue_page';

    /** @var \Ibexa\Workflow\Builder\StagesMapBuilder */
    private $stagesMapBuilder;

    /** @var \Ibexa\Workflow\Builder\CreatorsMapBuilder */
    private $creatorsMapBuilder;

    /** @var \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface */
    private $workflowService;

    /** @var \Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface */
    private $supportStrategy;

    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowDefinitionMetadataRegistry;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    public function __construct(
        Environment $twig,
        StagesMapBuilder $stagesMapBuilder,
        CreatorsMapBuilder $creatorsMapBuilder,
        WorkflowServiceInterface $workflowService,
        WorkflowSupportStrategyInterface $supportStrategy,
        WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry,
        RequestStack $requestStack,
        PermissionResolver $permissionResolver,
        ConfigResolverInterface $configResolver,
        UserService $userService,
        string $template = null,
        array $parameters = []
    ) {
        parent::__construct(
            $twig,
            $template ?? '@ibexadesign/ibexa_workflow/admin/dashboard/block/review_queue/block.html.twig',
            $parameters
        );

        $this->stagesMapBuilder = $stagesMapBuilder;
        $this->creatorsMapBuilder = $creatorsMapBuilder;
        $this->workflowService = $workflowService;
        $this->supportStrategy = $supportStrategy;
        $this->workflowDefinitionMetadataRegistry = $workflowDefinitionMetadataRegistry;
        $this->requestStack = $requestStack;
        $this->permissionResolver = $permissionResolver;
        $this->configResolver = $configResolver;
        $this->userService = $userService;
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $parameters = []): string
    {
        $workflowDefinitionMetadata = $this->workflowDefinitionMetadataRegistry->getAllWorkflowMetadata();

        // no need to render the block if there are no workflows defined
        if (count($workflowDefinitionMetadata) === 0) {
            return '';
        }

        $currentPage = $this->requestStack->getCurrentRequest()->query->getInt(self::PAGINATION_PARAM_NAME, 1);

        $workflowsMetadata = $this->filterWorkflowList(
            $this->workflowService->loadOngoingWorkflowMetadata(-1, 0)
        );

        $pagination = new Pagerfanta(new ArrayAdapter($workflowsMetadata));
        $pagination->setMaxPerPage($this->configResolver->getParameter('pagination.content_draft_limit'));
        $pagination->setCurrentPage(min(max($currentPage, 1), $pagination->getNbPages()));

        $blockParameters = [
            'data' => $pagination->getCurrentPageResults(),
            'creators_map' => $this->creatorsMapBuilder->buildCreatorsMapForWorkflowMetadataList(
                $pagination->getCurrentPageResults()
            ),
            'stages_map' => $this->stagesMapBuilder->buildStagesMapByWorkflow($pagination->getCurrentPageResults()),
            'workflow_definition_metadata' => $workflowDefinitionMetadata,
            'workflow_versions_details' => $this->buildWorkflowMetadataVersionDetails($workflowsMetadata),
            'pager' => $pagination,
            'pager_options' => ($parameters['pager_options'] ?? []) + [
                'pageParameter' => sprintf('[%s]', self::PAGINATION_PARAM_NAME),
            ],
        ];

        return parent::render(array_replace($parameters, $blockParameters));
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata[] $workflowMetadataList
     *
     * @return \Ibexa\Workflow\Value\WorkflowMetadata[]
     */
    private function filterWorkflowList(array $workflowMetadataList): array
    {
        $filter = new ChainListFilter([
            new ListFilter\SupportedWorkflowsOnlyListFilter($this->supportStrategy),
            new ListFilter\EditableWorkflowsListFilter($this->permissionResolver),
            new ListFilter\DraftsOnlyListFilter(),
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

class_alias(ReviewQueueBlock::class, 'EzSystems\EzPlatformWorkflow\Dashboard\Block\ReviewQueueBlock');
