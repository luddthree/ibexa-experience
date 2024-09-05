<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Controller;

use Ibexa\ActivityLog\Action;
use Ibexa\ActivityLog\ObjectClass;
use Ibexa\ActivityLog\Pagerfanta\ActivityLogGroupListAdapter;
use Ibexa\ActivityLog\Permission\PolicyProvider;
use Ibexa\Bundle\ActivityLog\Form\Data\ActivityLogSearchData;
use Ibexa\Bundle\ActivityLog\Form\Type\ActivityLogSearchType;
use Ibexa\Bundle\ActivityLog\View\ActivityLogGroupListView;
use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ActionCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LoggedAtCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LogicalOr;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ObjectCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ObjectNameCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\UserCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\LoggedAtSortClause;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class ActivityLogController extends Controller
{
    private ActivityLogServiceInterface $activityLogService;

    private ConfigResolverInterface $configResolver;

    private PermissionResolver $permissionResolver;

    public function __construct(
        ActivityLogServiceInterface $activityLogService,
        ConfigResolverInterface $configResolver,
        PermissionResolver $permissionResolver
    ) {
        $this->activityLogService = $activityLogService;
        $this->configResolver = $configResolver;
        $this->permissionResolver = $permissionResolver;
    }

    public function renderAction(Request $request): ActivityLogGroupListView
    {
        if ($this->permissionResolver->hasAccess(PolicyProvider::MODULE_ACTIVITY_LOG, 'read') === false) {
            throw new UnauthorizedException(PolicyProvider::MODULE_ACTIVITY_LOG, 'read');
        }

        $searchForm = $this->createSearchForm();

        $searchForm->handleRequest($request);

        $query = new Query();
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $this->applyFormData($searchForm->getData(), $query);
        }

        $query->sortClauses = [new LoggedAtSortClause(SortClauseInterface::DESC)];

        $activityLogs = new Pagerfanta(new ActivityLogGroupListAdapter($this->activityLogService, $query));
        $activityLogs->setMaxPerPage($this->configResolver->getParameter('activity_log.pagination.activity_logs_limit'));
        $activityLogs->setCurrentPage($request->query->getInt('page', 1));

        return new ActivityLogGroupListView(
            '@ibexadesign/activity_log/list.html.twig',
            $activityLogs,
            $searchForm,
        );
    }

    private function createSearchForm(): FormInterface
    {
        return $this->createForm(ActivityLogSearchType::class, null, [
            'csrf_protection' => false,
            'method' => Request::METHOD_GET,
        ]);
    }

    private function applyFormData(ActivityLogSearchData $data, Query $query): void
    {
        if (count($data->users) > 0) {
            $query->criteria[] = new UserCriterion(array_map(
                static fn (User $user): int => $user->getUserId(),
                $data->users,
            ));
        }

        if (count($data->objectClasses) > 0) {
            $query->criteria[] = new LogicalOr(array_map(
                static fn (ObjectClass $objectClass): ObjectCriterion => new ObjectCriterion($objectClass->getObjectClass()),
                $data->objectClasses,
            ));
        }

        if (count($data->actions) > 0) {
            $query->criteria[] = new ActionCriterion(
                array_map(
                    static fn (Action $action): string => $action->getName(),
                    $data->actions,
                ),
            );
        }

        if (isset($data->time)) {
            $query->criteria[] = new LoggedAtCriterion($data->time, LoggedAtCriterion::GT);
        }

        if (isset($data->query) && $data->query !== '') {
            $query->criteria[] = new ObjectNameCriterion($data->query, ObjectNameCriterion::OPERATOR_CONTAINS);
        }
    }
}
