<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Migration\Tests\StepExecutor\AbstractStepExecutorTest;

abstract class AbstractInitializedStepExecutorTest extends AbstractStepExecutorTest
{
    protected function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();
    }
}
