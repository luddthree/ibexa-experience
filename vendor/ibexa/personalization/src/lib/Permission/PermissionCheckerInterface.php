<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Permission;

/**
 * @internal
 */
interface PermissionCheckerInterface
{
    public function canEdit(int $customerId): bool;

    public function canView(int $customerId): bool;
}

class_alias(PermissionCheckerInterface::class, 'Ibexa\Platform\Personalization\Permission\PermissionCheckerInterface');
