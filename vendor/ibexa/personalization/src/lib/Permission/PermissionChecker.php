<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Permission;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Personalization\Factory\SecurityContextFactory;
use Ibexa\Personalization\Security\PersonalizationPolicyProvider;

/**
 * @internal
 */
final class PermissionChecker implements PermissionCheckerInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Personalization\Factory\SecurityContextFactory */
    private $securityContextFactory;

    public function __construct(
        PermissionResolver $permissionResolver,
        SecurityContextFactory $securityContextFactory
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->securityContextFactory = $securityContextFactory;
    }

    public function canEdit(int $customerId): bool
    {
        return $this->hasAccess($customerId, PersonalizationPolicyProvider::PERSONALIZATION_EDIT_FUNCTION);
    }

    public function canView(int $customerId): bool
    {
        return $this->hasAccess($customerId, PersonalizationPolicyProvider::PERSONALIZATION_VIEW_FUNCTION);
    }

    private function hasAccess(int $customerId, string $function): bool
    {
        return $this->permissionResolver->canUser(
            PersonalizationPolicyProvider::PERSONALIZATION_MODULE,
            $function,
            $this->securityContextFactory->createSecurityContextObject($customerId)
        );
    }
}

class_alias(PermissionChecker::class, 'Ibexa\Platform\Personalization\Permission\PermissionChecker');
