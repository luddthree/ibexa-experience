<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ReferenceDefinition\Role;

use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Webmozart\Assert\Assert;

final class RoleIdResolver implements RoleResolverInterface
{
    public static function getHandledType(): string
    {
        return 'role_id';
    }

    public function resolve(ReferenceDefinition $referenceDefinition, Role $role): Reference
    {
        $name = $referenceDefinition->getName();
        Assert::notNull(
            $role->id,
            'Role object does not contain an ID. Make sure to reload Role object if persisted.'
        );

        return Reference::create($name, $role->id);
    }
}

class_alias(RoleIdResolver::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceDefinition\Role\RoleIdResolver');
