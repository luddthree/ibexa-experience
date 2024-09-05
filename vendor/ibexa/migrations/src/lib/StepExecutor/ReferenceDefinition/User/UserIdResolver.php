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
use Webmozart\Assert\Assert;

final class UserIdResolver implements UserResolverInterface
{
    public static function getHandledType(): string
    {
        return 'user_id';
    }

    public function resolve(ReferenceDefinition $referenceDefinition, User $user): Reference
    {
        $name = $referenceDefinition->getName();
        Assert::notNull(
            $user->id,
            'User object does not contain an ID. Make sure to reload User object if persisted.'
        );

        return Reference::create($name, $user->id);
    }
}
