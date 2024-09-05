<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations;

use Ibexa\Migration\Tests\StepExecutor\AbstractStepExecutorTest as BaseAbstractStepExecutorTest;
use Ibexa\Tests\Integration\ProductCatalog\KernelCommonTestTrait;

abstract class AbstractStepExecutorTest extends BaseAbstractStepExecutorTest
{
    use KernelCommonTestTrait;
}
