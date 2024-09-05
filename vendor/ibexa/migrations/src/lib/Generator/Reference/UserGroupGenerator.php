<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Reference;

use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\UserGroup\UserGroupIdResolver;
use Webmozart\Assert\Assert;

final class UserGroupGenerator extends AbstractReferenceGenerator
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup
     */
    public function generate(ValueObject $userGroup): array
    {
        Assert::isInstanceOf($userGroup, UserGroup::class);

        return $this->generateReferences($userGroup->getContentType()->identifier, $userGroup->getName());
    }

    protected function getReferenceMetadata(): array
    {
        return [
            new ReferenceMetadata('ref__user_group', UserGroupIdResolver::getHandledType()),
        ];
    }
}

class_alias(UserGroupGenerator::class, 'Ibexa\Platform\Migration\Generator\Reference\UserGroupGenerator');
