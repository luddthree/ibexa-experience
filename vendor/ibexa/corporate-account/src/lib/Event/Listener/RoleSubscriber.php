<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Event\Listener;

use Ibexa\Contracts\Core\Repository\Events\Role\AssignRoleToUserEvent;
use Ibexa\Contracts\Core\Repository\Events\Role\RemoveRoleAssignmentEvent;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\User\UserRoleAssignment;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment\Handler;
use Ibexa\CorporateAccount\Persistence\Values\MemberAssignmentCreateStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RoleSubscriber implements EventSubscriberInterface
{
    private CorporateAccountConfiguration $corporateAccountConfiguration;

    private LocationService $locationService;

    private RoleService $roleService;

    private Handler $handler;

    public function __construct(
        CorporateAccountConfiguration $corporateAccountConfiguration,
        LocationService $locationService,
        RoleService $roleService,
        Handler $handler
    ) {
        $this->corporateAccountConfiguration = $corporateAccountConfiguration;
        $this->locationService = $locationService;
        $this->roleService = $roleService;
        $this->handler = $handler;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): iterable
    {
        return [
            AssignRoleToUserEvent::class => 'assignRole',
            RemoveRoleAssignmentEvent::class => 'removeRoleAssignment',
        ];
    }

    public function assignRole(AssignRoleToUserEvent $event): void
    {
        $rolesIdentifiers = $this->corporateAccountConfiguration->getCorporateAccountsRolesIdentifiers();
        $role = $event->getRole();

        if (!in_array($role->identifier, $rolesIdentifiers, true)) {
            return;
        }
        $roleLimitation = $event->getRoleLimitation();

        if ($roleLimitation === null || $roleLimitation->getIdentifier() !== 'Subtree') {
            return;
        }

        $roleAssignments = $this->roleService->getRoleAssignmentsForUser($event->getUser());
        foreach ($roleAssignments as $roleAssignment) {
            if ($roleAssignment->getRole()->identifier !== $role->identifier) {
                continue;
            }

            $roleAssignmentLimitation = $roleAssignment->getRoleLimitation();
            if ($roleAssignmentLimitation === null) {
                continue;
            }

            if ($roleAssignmentLimitation->limitationValues === $roleLimitation->limitationValues) {
                $roleAssignmentId = $roleAssignment->id;
                break;
            }
        }

        if (empty($roleAssignmentId)) {
            throw new BadStateException('roleAssignmentId', 'Assignment does not exist');
        }

        $companyPath = reset($roleLimitation->limitationValues);
        $companyPathParts = array_filter(explode('/', $companyPath));

        $companyLocationId = (int)end($companyPathParts);
        $companyLocation = $this->locationService->loadLocation($companyLocationId);

        $memberAssignmentCreateStruct = new MemberAssignmentCreateStruct(
            $event->getUser()->id,
            $role->identifier,
            $roleAssignmentId,
            $companyLocation->getContent()->id,
            $companyLocationId
        );

        $this->handler->create($memberAssignmentCreateStruct);
    }

    public function removeRoleAssignment(RemoveRoleAssignmentEvent $event): void
    {
        $rolesIdentifiers = $this->corporateAccountConfiguration->getCorporateAccountsRolesIdentifiers();
        $roleAssigment = $event->getRoleAssignment();
        $role = $roleAssigment->getRole();
        $roleLimitation = $roleAssigment->getRoleLimitation();

        if (!in_array($role->identifier, $rolesIdentifiers, true)) {
            return;
        }

        if (!$roleAssigment instanceof UserRoleAssignment) {
            return;
        }

        if ($roleLimitation === null || $roleLimitation->getIdentifier() !== 'Subtree') {
            return;
        }

        $companyPath = reset($roleLimitation->limitationValues);
        $companyPathParts = array_filter(explode('/', $companyPath));

        $companyLocationId = (int)end($companyPathParts);
        $companyLocation = $this->locationService->loadLocation($companyLocationId);

        /** @var \Ibexa\CorporateAccount\Persistence\Values\MemberAssignment $memberAssignment */
        $memberAssignment = $this->handler->findBy([
            'member_id' => $roleAssigment->getUser()->id,
            'company_id' => $companyLocation->getContent()->id,
            'company_location_id' => $companyLocation->id,
            'member_role' => $role->identifier,
        ])[0];

        $this->handler->delete($memberAssignment->id);
    }
}
