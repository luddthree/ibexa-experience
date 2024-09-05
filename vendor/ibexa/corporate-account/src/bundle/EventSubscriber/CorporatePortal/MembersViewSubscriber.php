<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber\CorporatePortal;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\View\CorporatePortal\MembersView;

final class MembersViewSubscriber extends AbstractViewSubscriber
{
    private PermissionResolver $permissionResolver;

    private UserService $userService;

    private RoleService $roleService;

    private CorporateAccountConfiguration $corporateAccountConfiguration;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        UserService $userService,
        PermissionResolver $permissionResolver,
        ConfigResolverInterface $configResolver,
        RoleService $roleService,
        CorporateAccountConfiguration $corporateAccountConfiguration
    ) {
        parent::__construct($siteAccessService, $configResolver);

        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->corporateAccountConfiguration = $corporateAccountConfiguration;
    }

    protected function supports(View $view): bool
    {
        return $view instanceof MembersView;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\CorporatePortal\MembersView $view
     */
    protected function configureView(View $view): void
    {
        $company = $view->getCompany();

        $membersGroup = $this->userService->loadUserGroup($company->getMembersId());

        $canEditMember = [];
        $canAssignRole = [];

        $roles = array_map(
            fn (string $roleIdentifier): Role => $this->roleService->loadRoleByIdentifier($roleIdentifier),
            $this->corporateAccountConfiguration->getCorporateAccountsRolesIdentifiers(),
        );

        foreach ($view->getMembers() as $member) {
            $canEditMember[$member->getId()] = $this->permissionResolver->canUser(
                'content',
                'edit',
                $member->getUser()
            );
            $canAssignRole[$member->getId()] = $this->permissionResolver->canUser(
                'role',
                'assign',
                $member->getUser(),
                $roles
            );
        }

        $view->addParameters([
            'can_invite' => $this->permissionResolver->canUser(
                'user',
                'invite',
                $membersGroup
            ),
            'can_assign_role_map' => $canAssignRole,
            'can_edit_member_map' => $canEditMember,
        ]);
    }
}
