<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Migration\StepExecutor\ServiceCallExecuteStepExecutor;
use Ibexa\Migration\ValueObject\Step\ServiceCallExecuteStep;
use Ibexa\Tests\Bundle\Migration\FooService;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * @covers \Ibexa\Migration\StepExecutor\ServiceCallExecuteStepExecutor
 */
final class ServiceCallExecuteStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    public function testHandle(): void
    {
        $fooService = self::getServiceByClassName(FooService::class, '__foo_service__');
        self::assertSame(0, $fooService->calledTimes);

        $step = new ServiceCallExecuteStep('__foo_service__', [], '__invoke');

        $executor = self::getServiceByClassName(ServiceCallExecuteStepExecutor::class);
        self::assertTrue($executor->canHandle($step));
        $executor->handle($step);
        self::assertSame(1, $fooService->calledTimes);

        $step = new ServiceCallExecuteStep('__non_existent_service__', [], '__non_existent_method');
        self::assertTrue($executor->canHandle($step));
        self::expectException(ServiceNotFoundException::class);
        self::expectExceptionMessage('Service "__non_existent_service__" not found: the container inside "Symfony\Component\DependencyInjection\Argument\ServiceLocator" is a smaller service locator that only knows about the "__foo_service__" service.');
        $executor->handle($step);
    }
}

class_alias(ServiceCallExecuteStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\ServiceCallExecuteStepExecutorTest');
