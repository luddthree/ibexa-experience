<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Reference;

use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\Role\RoleIdResolver;
use Webmozart\Assert\Assert;

final class RoleGenerator extends AbstractReferenceGenerator
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\User\Role $role
     */
    public function generate(ValueObject $role): array
    {
        Assert::isInstanceOf($role, Role::class);

        return $this->generateReferences((string)$role->id, 'role_id');
    }

    protected function getReferenceMetadata(): array
    {
        return [
            new ReferenceMetadata('ref__role', RoleIdResolver::getHandledType()),
        ];
    }
}

class_alias(RoleGenerator::class, 'Ibexa\Platform\Migration\Generator\Reference\RoleGenerator');
