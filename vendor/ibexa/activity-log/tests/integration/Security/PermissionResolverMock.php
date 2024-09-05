<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\Security;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\User\LookupLimitationResult;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This mock service allows fine-tuning of security during tests, without performing user data change.
 *
 * @phpstan-type TPermissionResolverAccess bool|array<
 *     array{
 *         limitation: \Ibexa\Contracts\Core\Repository\Values\User\Limitation|null,
 *         policies: array<\Ibexa\Contracts\Core\Repository\Values\User\Policy>
 *     },
 * >
 */
final class PermissionResolverMock implements PermissionResolver
{
    private PermissionResolver $permissionResolver;

    /**
     * @var TPermissionResolverAccess|null
     */
    private $access = null;

    public function __construct(PermissionResolver $permissionResolver)
    {
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @phpstan-param TPermissionResolverAccess|null $access
     */
    public function setHasAccessResult($access): void
    {
        $this->access = $access;
    }

    public function getCurrentUserReference(): UserReference
    {
        return $this->permissionResolver->getCurrentUserReference();
    }

    public function setCurrentUserReference(UserReference $userReference): void
    {
        $this->permissionResolver->setCurrentUserReference($userReference);
    }

    public function hasAccess(string $module, string $function, ?UserReference $userReference = null)
    {
        return $this->access ?? $this->permissionResolver->hasAccess($module, $function, $userReference);
    }

    public function canUser(string $module, string $function, ValueObject $object, array $targets = []): bool
    {
        return $this->permissionResolver->canUser($module, $function, $object, $targets);
    }

    public function lookupLimitations(
        string $module,
        string $function,
        ValueObject $object,
        array $targets = [],
        array $limitationsIdentifiers = []
    ): LookupLimitationResult {
        return $this->permissionResolver->lookupLimitations(
            $module,
            $function,
            $object,
            $targets,
            $limitationsIdentifiers,
        );
    }
}
