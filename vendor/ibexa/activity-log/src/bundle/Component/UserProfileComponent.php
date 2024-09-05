<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Component;

use Ibexa\ActivityLog\Permission\PolicyProvider;
use Ibexa\Bundle\ActivityLog\Form\Data\ActivityLogSearchData;
use Ibexa\Bundle\ActivityLog\Form\Type\ActivityLogSearchType;
use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\UserCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\LoggedAtSortClause;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface;
use Ibexa\Contracts\AdminUi\Component\Renderable;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

final class UserProfileComponent implements Renderable
{
    private const DEFAULT_LIMIT = 20;

    private Environment $twig;

    private PermissionResolver $permissionResolver;

    private ActivityLogServiceInterface $activityLogService;

    private UserService $userService;

    private FormFactoryInterface $formFactory;

    public function __construct(
        Environment $twig,
        PermissionResolver $permissionResolver,
        ActivityLogServiceInterface $activityLogService,
        UserService $userService,
        FormFactoryInterface $formFactory
    ) {
        $this->twig = $twig;
        $this->permissionResolver = $permissionResolver;
        $this->activityLogService = $activityLogService;
        $this->userService = $userService;
        $this->formFactory = $formFactory;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function render(array $parameters = []): string
    {
        if ($this->permissionResolver->hasAccess(PolicyProvider::MODULE_ACTIVITY_LOG, 'read') === false) {
            return '';
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserReference $user */
        $user = $parameters['user'] ?? $this->permissionResolver->getCurrentUserReference();
        /** @var int $limit */
        $limit = $parameters['limit'] ?? self::DEFAULT_LIMIT;

        $query = new Query();
        $query->criteria[] = new UserCriterion([$user->getUserId()]);
        $query->sortClauses = [
            new LoggedAtSortClause(SortClauseInterface::DESC),
        ];
        $query->limit = $limit;

        $activityLogs = $this->activityLogService->findGroups($query)->getActivityLogs();
        $form = $this->createSearchForm($user);

        return $this->twig->render(
            '@ibexadesign/user_profile/activity_log.html.twig',
            [
                'activity_logs' => $activityLogs,
                'search_form' => $form->createView(),
            ]
        );
    }

    private function createSearchForm(UserReference $userReference): FormInterface
    {
        $data = new ActivityLogSearchData();
        try {
            $data->users[] = $this->userService->loadUser($userReference->getUserId());
        } catch (NotFoundException $e) {
            // Invalid user
        }

        return $this->formFactory->create(ActivityLogSearchType::class, $data, [
            'csrf_protection' => false,
            'method' => Request::METHOD_GET,
        ]);
    }
}
