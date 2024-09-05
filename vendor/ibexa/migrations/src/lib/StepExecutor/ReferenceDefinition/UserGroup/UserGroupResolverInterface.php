<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ReferenceDefinition\UserGroup;

use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;

/**
 * @internal
 */
interface UserGroupResolverInterface
{
    public function resolve(ReferenceDefinition $referenceDefinition, UserGroup $userGroup): Reference;
}

class_alias(UserGroupResolverInterface::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceDefinition\UserGroup\UserGroupResolverInterface');
