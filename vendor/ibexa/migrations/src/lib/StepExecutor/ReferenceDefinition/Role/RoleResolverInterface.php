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

/**
 * @internal
 */
interface RoleResolverInterface
{
    public function resolve(ReferenceDefinition $referenceDefinition, Role $role): Reference;
}

class_alias(RoleResolverInterface::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceDefinition\Role\RoleResolverInterface');
