<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Permission;

use Ibexa\Contracts\Core\Repository\PermissionResolver as APIPermissionResolver;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\ProductCatalog\Exception\UnauthorizedException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class PermissionResolver implements PermissionResolverInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private APIPermissionResolver $permissionResolver;

    private ContextResolver $permissionContextResolver;

    private bool $isDebug;

    public function __construct(
        APIPermissionResolver $permissionResolver,
        ContextResolver $permissionContextResolver,
        bool $isDebug,
        ?LoggerInterface $logger = null
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->permissionContextResolver = $permissionContextResolver;
        $this->logger = $logger ?? new NullLogger();
        $this->isDebug = $isDebug;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function canUser(
        PolicyInterface $policy
    ): bool {
        $module = $policy->getModule();
        $function = $policy->getFunction();
        $object = $policy->getObject();

        if ($object === null) {
            return $this->hasAccess($module, $function);
        }

        try {
            $permissionContext = $this->permissionContextResolver->resolve($policy);
        } catch (InvalidArgumentException $e) {
            if ($this->isDebug) {
                throw $e;
            }

            $this->logger->error($e->getMessage());

            return $this->hasAccess($module, $function);
        }

        return $this->permissionResolver->canUser(
            $module,
            $function,
            $permissionContext->getObject(),
            $permissionContext->getTargets(),
        );
    }

    public function assertPolicy(PolicyInterface $policy): void
    {
        if (!$this->canUser($policy)) {
            throw new UnauthorizedException($policy);
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function hasAccess(string $module, string $function): bool
    {
        return $this->permissionResolver->hasAccess($module, $function) !== false;
    }
}
