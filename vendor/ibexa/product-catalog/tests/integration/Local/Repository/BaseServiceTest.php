<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

abstract class BaseServiceTest extends IbexaKernelTestCase
{
    /**
     * @param array<array<string, mixed>> $policiesData
     */
    protected function createRoleWithPolicies(
        string $roleName,
        array $policiesData
    ): Role {
        $roleService = self::getServiceByClassName(RoleService::class);
        $roleCreateStruct = $roleService->newRoleCreateStruct($roleName);
        foreach ($policiesData as $policyData) {
            $policyCreateStruct = $roleService->newPolicyCreateStruct(
                $policyData['module'],
                $policyData['function']
            );

            if (isset($policyData['limitations'])) {
                foreach ($policyData['limitations'] as $limitation) {
                    $policyCreateStruct->addLimitation($limitation);
                }
            }

            $roleCreateStruct->addPolicy($policyCreateStruct);
        }

        $roleDraft = $roleService->createRole($roleCreateStruct);

        $roleService->publishRoleDraft($roleDraft);

        return $roleService->loadRole($roleDraft->id);
    }

    /**
     * @param array<array<string, mixed>> $policiesData
     */
    protected function createUserWithPolicies(
        string $login,
        array $policiesData,
        RoleLimitation $roleLimitation = null
    ): User {
        $repository = self::getServiceByClassName(Repository::class);
        $roleService = $repository->getRoleService();
        $userService = $repository->getUserService();

        $repository->beginTransaction();
        try {
            $userCreateStruct = $userService->newUserCreateStruct(
                $login,
                "{$login}@test.local",
                $login,
                'eng-GB'
            );
            $userCreateStruct->setField('first_name', $login);
            $userCreateStruct->setField('last_name', $login);
            $user = $userService->createUser($userCreateStruct, [$userService->loadUserGroup(4)]);

            $role = $this->createRoleWithPolicies(uniqid('role_for_' . $login . '_', true), $policiesData);
            $roleService->assignRoleToUser($role, $user, $roleLimitation);

            $repository->commit();

            return $user;
        } catch (ForbiddenException | NotFoundException | UnauthorizedException $ex) {
            $repository->rollback();
            throw $ex;
        }
    }
}
