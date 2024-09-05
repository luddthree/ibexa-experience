<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Security\Authorization;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class PolicyVoter implements VoterInterface
{
    private PermissionResolver $permissionResolver;

    public function __construct(PermissionResolver $permissionResolver)
    {
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * Checks if the voter supports the given attribute.
     *
     * @param mixed $attribute An attribute
     *
     * @return bool true if this Voter supports the attribute, false otherwise
     */
    public function supportsAttribute($attribute): bool
    {
        return $attribute instanceof PolicyInterface;
    }

    /**
     * @param array<mixed> $attributes
     */
    public function vote(TokenInterface $token, $object, array $attributes): int
    {
        foreach ($attributes as $attribute) {
            if ($this->supportsAttribute($attribute)) {
                /** @var \Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface $attribute */
                if ($this->permissionResolver->hasAccess($attribute->getModule(), $attribute->getFunction()) === false) {
                    return VoterInterface::ACCESS_DENIED;
                }

                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
