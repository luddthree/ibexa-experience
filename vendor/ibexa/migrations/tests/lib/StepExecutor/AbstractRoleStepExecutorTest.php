<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverterManager;
use Ibexa\Migration\StepExecutor\ActionExecutor;

abstract class AbstractRoleStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    final protected static function getRoleActionExecutor(): ActionExecutor\ExecutorInterface
    {
        return self::getServiceByClassName(ActionExecutor\Role\Executor::class);
    }

    final protected static function getLimitationConverter(): LimitationConverterManager
    {
        return self::getServiceByClassName(LimitationConverterManager::class);
    }
}
