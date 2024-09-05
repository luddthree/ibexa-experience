<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\Repository\Values\User\UserReference;
use LogicException;

trait UserContextAwareStepExecutorTrait
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserReference|null */
    private $previousApiUser;

    public function setPermissionResolver(PermissionResolver $permissionResolver): void
    {
        $this->permissionResolver = $permissionResolver;
    }

    public function loginApiUser(?int $creatorId): void
    {
        if (null === $creatorId) {
            return;
        }

        if (null !== $this->previousApiUser) {
            throw new LogicException('Cannot overwrite User reference twice in the same step executor.');
        }

        $userReference = new UserReference($creatorId);
        $this->previousApiUser = $this->permissionResolver->getCurrentUserReference();
        $this->permissionResolver->setCurrentUserReference($userReference);
    }

    public function restorePreviousApiUser(): void
    {
        if (isset($this->previousApiUser)) {
            $this->permissionResolver->setCurrentUserReference($this->previousApiUser);
            $this->previousApiUser = null;
        }
    }
}

class_alias(UserContextAwareStepExecutorTrait::class, 'Ibexa\Platform\Migration\StepExecutor\UserContextAwareStepExecutorTrait');
