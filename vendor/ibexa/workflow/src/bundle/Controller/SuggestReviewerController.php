<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Workflow\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Core\Repository\Values\User\UserReference;
use Ibexa\Workflow\QueryType\UsersQueryType;
use Ibexa\Workflow\Security\Limitation\IgnoreVersionLockLimitation;
use Ibexa\Workflow\Value\SuggestedReviewer;
use Ibexa\Workflow\Value\WorkflowTransitionDefinitionMetadata;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class SuggestReviewerController extends Controller
{
    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    private $searchService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configurationResolver;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowMetadataRegistry;

    public function __construct(
        SearchService $searchService,
        PermissionResolver $permissionResolver,
        ConfigResolverInterface $configurationResolver,
        ContentService $contentService,
        LocationService $locationService,
        UsersQueryType $usersQueryType,
        WorkflowDefinitionMetadataRegistryInterface $workflowMetadataRegistry
    ) {
        $this->searchService = $searchService;
        $this->configurationResolver = $configurationResolver;
        $this->permissionResolver = $permissionResolver;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->usersQueryType = $usersQueryType;
        $this->workflowMetadataRegistry = $workflowMetadataRegistry;
    }

    public function findForContentCreateAction(
        string $workflowName,
        string $transitionName,
        ContentType $contentType,
        string $languageCode,
        int $locationId,
        Request $request
    ): JsonResponse {
        $query = $request->query->get('query', '');
        $limit = $request->query->getInt(
            'limit',
            $this->configurationResolver->getParameter('workflows_config.pagination.suggested_reviewers_limit')
        );

        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $languageCode);
        $locationCreateStruct = $this->locationService->newLocationCreateStruct($locationId, $contentType);
        $transitionMetadata = $this->getTransitionMetadata($workflowName, $transitionName);

        $users = $this->findUsers($limit, $query, $transitionMetadata->getReviewersMetadata()->getUserGroup());
        $users = $this->convertToResultObjects($users);
        $users = $this->addPermissonInfo($users, $contentCreateStruct, [$locationCreateStruct]);

        return new JsonResponse($users);
    }

    public function findForContentEditAction(
        string $workflowName,
        string $transitionName,
        VersionInfo $versionInfo,
        Location $location,
        Request $request
    ): JsonResponse {
        $query = $request->query->get('query', '');
        $limit = $request->query->getInt(
            'limit',
            $this->configurationResolver->getParameter('workflows_config.pagination.suggested_reviewers_limit')
        );

        $transitionMetadata = $this->getTransitionMetadata($workflowName, $transitionName);

        $users = $this->findUsers($limit, $query, $transitionMetadata->getReviewersMetadata()->getUserGroup());
        $users = $this->convertToResultObjects($users);
        $users = $this->addPermissonInfo($users, $versionInfo, [$location]);

        return new JsonResponse($users);
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\ContentInfo[]
     */
    public function findUsers(int $limit, string $query, ?int $groupId = null): array
    {
        $params = [
            'query' => $query,
            'limit' => $limit,
        ];

        if ($groupId) {
            $params['groupId'] = $groupId;
        }

        $query = $this->usersQueryType->getQuery($params);
        $result = $this->searchService->findContent($query);

        return array_map(static function ($item) {
            return $item->valueObject->versionInfo->contentInfo;
        }, $result->searchHits);
    }

    /**
     * @param \Ibexa\Workflow\Value\SuggestedReviewer[] $users
     *
     * @return \Ibexa\Workflow\Value\SuggestedReviewer[]
     */
    private function addPermissonInfo(array $users, ValueObject $object, array $targets = []): array
    {
        $currentUser = $this->permissionResolver->getCurrentUserReference();
        $targets[] = new IgnoreVersionLockLimitation();

        $users = array_map(function (SuggestedReviewer $user) use ($object, $targets) {
            $userReference = new UserReference($user->id);
            $this->permissionResolver->setCurrentUserReference($userReference);

            $user->canReview = $this->permissionResolver->canUser('content', 'read', $object, $targets)
                && $this->permissionResolver->canUser('content', 'edit', $object, $targets);

            return $user;
        }, $users);
        $this->permissionResolver->setCurrentUserReference($currentUser);

        return $users;
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content\ContentInfo[] $list
     *
     * @return \Ibexa\Workflow\Value\SuggestedReviewer[]
     */
    private function convertToResultObjects(array $list): array
    {
        return array_map(static function ($item) {
            return new SuggestedReviewer([
                'id' => $item->id,
                'name' => $item->name,
            ]);
        }, $list);
    }

    private function getTransitionMetadata(string $workflowName, string $transitonName): WorkflowTransitionDefinitionMetadata
    {
        $workflowMetadata = $this->workflowMetadataRegistry->getWorkflowMetadata($workflowName);

        return $workflowMetadata->getTransitionMetadata($transitonName);
    }
}

class_alias(SuggestReviewerController::class, 'EzSystems\EzPlatformWorkflowBundle\Controller\SuggestReviewerController');
