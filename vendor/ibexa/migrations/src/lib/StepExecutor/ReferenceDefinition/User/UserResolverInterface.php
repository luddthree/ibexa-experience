<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ReferenceDefinition\User;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;

/**
 * @internal
 */
interface UserResolverInterface
{
    public function resolve(ReferenceDefinition $referenceDefinition, User $user): Reference;
}
