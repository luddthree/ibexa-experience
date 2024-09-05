<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Security\Service;

interface SecurityServiceInterface
{
    public function getCurrentCustomerId(): ?int;

    public function hasGrantedAccess(): bool;

    /**
     * @return array<int|string>
     */
    public function getGrantedAccessList(): array;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function checkAccess(int $customerId): void;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function checkAcceptanceStatus(): void;
}

class_alias(SecurityServiceInterface::class, 'Ibexa\Platform\Personalization\Security\Service\SecurityServiceInterface');
