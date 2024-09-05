<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\PermissionResolver;

interface UserContextAwareStepExecutorInterface
{
    public function setPermissionResolver(PermissionResolver $permissionResolver): void;

    public function loginApiUser(?int $creatorId): void;

    public function restorePreviousApiUser(): void;
}

class_alias(UserContextAwareStepExecutorInterface::class, 'Ibexa\Platform\Migration\StepExecutor\UserContextAwareStepExecutorInterface');
