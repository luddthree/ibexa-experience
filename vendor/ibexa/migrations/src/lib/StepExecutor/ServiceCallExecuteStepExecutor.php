<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Step\ServiceCallExecuteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

final class ServiceCallExecuteStepExecutor implements StepExecutorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Psr\Container\ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container, ?LoggerInterface $logger = null)
    {
        $this->container = $container;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ServiceCallExecuteStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\StepInterface|\Ibexa\Migration\ValueObject\Step\ServiceCallExecuteStep $step
     */
    public function handle(StepInterface $step): void
    {
        Assert::isInstanceOf($step, ServiceCallExecuteStep::class);

        $service = $this->container->get($step->service);
        $method = $step->method;
        $arguments = $step->arguments;

        $service->{$method}(...$arguments);

        $this->getLogger()->notice(sprintf(
            'Called service method: %s::%s (key: %s) with %d arguments',
            get_class($service),
            $method,
            $step->service,
            count($arguments),
        ));
    }
}

class_alias(ServiceCallExecuteStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ServiceCallExecuteStepExecutor');
