<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value as CustomerGroupValue;

final class CustomerGroupResolver implements CustomerGroupResolverInterface
{
    private PermissionResolver $permissionResolver;

    private UserService $userService;

    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(
        PermissionResolver $permissionResolver,
        UserService $userService,
        CustomerGroupServiceInterface $customerGroupService
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
        $this->customerGroupService = $customerGroupService;
    }

    public function resolveCustomerGroup(?User $user = null): ?CustomerGroupInterface
    {
        $user ??= $this->getCurrentUser();

        foreach ($user->getFields() as $field) {
            if ($field->value instanceof CustomerGroupValue && $field->value->getId() !== null) {
                return $this->customerGroupService->getCustomerGroup(
                    $field->value->getId()
                );
            }
        }

        return null;
    }

    private function getCurrentUser(): User
    {
        return $this->userService->loadUser(
            $this->permissionResolver->getCurrentUserReference()->getUserId()
        );
    }
}
