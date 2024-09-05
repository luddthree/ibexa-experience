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
use Webmozart\Assert\Assert;

final class UserGroupIdResolver implements UserGroupResolverInterface
{
    public static function getHandledType(): string
    {
        return 'user_group_id';
    }

    public function resolve(ReferenceDefinition $referenceDefinition, UserGroup $userGroup): Reference
    {
        $name = $referenceDefinition->getName();
        Assert::notNull(
            $userGroup->id,
            'User Group object does not contain an ID. Make sure to reload User Group object if persisted.'
        );

        return Reference::create($name, $userGroup->id);
    }
}

class_alias(UserGroupIdResolver::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceDefinition\UserGroup\UserGroupIdResolver');
