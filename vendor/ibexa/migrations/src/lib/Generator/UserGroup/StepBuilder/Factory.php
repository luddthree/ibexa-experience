<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\UserGroup\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\AbstractStepFactory;
use Webmozart\Assert\Assert;

final class Factory extends AbstractStepFactory
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroup
     */
    protected function prepareLogMessage(ValueObject $userGroup, Mode $mode, string $type): ?string
    {
        Assert::isInstanceOf($userGroup, UserGroup::class);

        return sprintf('[Step] Preparing user group create for %s', $userGroup->getName());
    }
}

class_alias(Factory::class, 'Ibexa\Platform\Migration\Generator\UserGroup\StepBuilder\Factory');
