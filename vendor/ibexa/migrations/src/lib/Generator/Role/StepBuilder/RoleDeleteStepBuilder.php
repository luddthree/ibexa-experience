<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Role\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\ValueObject\Step\Role\Matcher;
use Ibexa\Migration\ValueObject\Step\RoleDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

final class RoleDeleteStepBuilder implements StepBuilderInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\User\Role $valueObject
     */
    public function build(ValueObject $valueObject): StepInterface
    {
        Assert::isInstanceOf($valueObject, Role::class);

        return new RoleDeleteStep(
            new Matcher(Matcher::IDENTIFIER, $valueObject->identifier)
        );
    }
}

class_alias(RoleDeleteStepBuilder::class, 'Ibexa\Platform\Migration\Generator\Role\StepBuilder\RoleDeleteStepBuilder');
