<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Role\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\AbstractStepFactory;
use function sprintf;
use Webmozart\Assert\Assert;

final class RoleStepFactory extends AbstractStepFactory
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\User\Role $valueObject
     */
    protected function prepareLogMessage(ValueObject $valueObject, Mode $mode, string $type): ?string
    {
        Assert::isInstanceOf($valueObject, Role::class);

        return sprintf('[Step] Preparing %s %s for %s', $type, $mode->getMode(), $valueObject->identifier);
    }
}

class_alias(RoleStepFactory::class, 'Ibexa\Platform\Migration\Generator\Role\StepBuilder\RoleStepFactory');
