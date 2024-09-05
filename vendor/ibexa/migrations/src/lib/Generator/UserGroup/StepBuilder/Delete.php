<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\UserGroup\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserGroupDeleteStep;
use Ibexa\Migration\ValueObject\UserGroup\Matcher;
use Webmozart\Assert\Assert;

final class Delete implements StepBuilderInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup
     */
    public function build(ValueObject $userGroup): StepInterface
    {
        Assert::isInstanceOf($userGroup, UserGroup::class);

        return new UserGroupDeleteStep(
            new Matcher(Matcher::REMOTE_ID, $userGroup->contentInfo->remoteId),
        );
    }
}

class_alias(Delete::class, 'Ibexa\Platform\Migration\Generator\UserGroup\StepBuilder\Delete');
