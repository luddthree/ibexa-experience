<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount\Migrations\StepExecutor;

use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Tests\Integration\CorporateAccount\IbexaKernelTestCase;
use Psr\Container\ContainerInterface;

abstract class AbstractStepExecutorTest extends IbexaKernelTestCase
{
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
            CollectorInterface::class => self::getServiceByClassName(CollectorInterface::class),
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

    protected static function getReferenceResolver(string $resolver): ResolverInterface
    {
        $resolver = 'ibexa.migrations.reference_definition.resolver.' . $resolver;

        return self::getServiceByClassName(ResolverInterface::class, $resolver);
    }
}
