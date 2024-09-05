<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Security\EditorialMode;

interface TokenRevokerInterface
{
    /**
     * Returns true if given token has not been revoked.
     */
    public function isValid(array $token): bool;

    /**s
     * Revokes given token.
     */
    public function revoke(array $token): void;
}

class_alias(TokenRevokerInterface::class, 'EzSystems\EzPlatformPageBuilder\Security\EditorialMode\TokenRevokerInterface');
