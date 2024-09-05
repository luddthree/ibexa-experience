<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Tests\StepExecutor;

use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\Tests\MigrationTestServicesTrait;
use Psr\Container\ContainerInterface;

abstract class AbstractStepExecutorTest extends IbexaKernelTestCase
{
    use MigrationTestServicesTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();
    }

    /**
     * @param array<string, object> $serviceMap
     */
    final protected function configureExecutor(AbstractStepExecutor $executor, array $serviceMap = []): void
    {
        $container = $this->createContainerForServiceMap($serviceMap);
        $executor->setContainer($container);
        $executor->setPermissionResolver(self::getPermissionResolver());
    }

    /**
     * @return array<class-string, object>
     */
    private function getDefaultServicesForContainer(): array
    {
        return [
            TransactionHandler::class => self::getTransactionHandler(),
            CollectorInterface::class => self::getCollector(),
        ];
    }

    /**
     * @param array<string, object> $serviceMap
     */
    private function createContainerForServiceMap(array $serviceMap, bool $mergeDefaults = true): ContainerInterface
    {
        if ($mergeDefaults) {
            $serviceMap = array_merge($this->getDefaultServicesForContainer(), $serviceMap);
        }

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('get')
            ->willReturnCallback(
                static function (string $id) use ($serviceMap): object {
                    if (isset($serviceMap[$id])) {
                        return $serviceMap[$id];
                    }

                    throw new \LogicException("Missing '$id' service for tests");
                }
            );

        $container
            ->method('has')
            ->willReturnCallback(
                static function (string $id) use ($serviceMap): bool {
                    return isset($serviceMap[$id]);
                }
            );

        return $container;
    }
}

class_alias(AbstractStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\AbstractStepExecutorTest');
