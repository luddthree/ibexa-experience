<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\StepExecutor\ActionExecutor\Exception\InvalidActionException;
use Ibexa\Migration\ValueObject\Step\Action;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\ServiceLocator;

abstract class AbstractExecutor implements ExecutorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Symfony\Component\DependencyInjection\ServiceLocator */
    private $serviceLocator;

    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->logger = new NullLogger();
    }

    final public function handle(Action $action, ValueObject $valueObject): void
    {
        $executor = $this->findExecutor($action);
        $executor->handle($action, $valueObject);
        $this->log($action, $valueObject);
    }

    private function findExecutor(Action $action): ExecutorInterface
    {
        if (false === $this->serviceLocator->has($action->getSupportedType())) {
            throw new InvalidActionException($action, $this->getSupportedActions());
        }

        return $this->serviceLocator->get($action->getSupportedType());
    }

    private function log(Action $action, ValueObject $valueObject): void
    {
        $message = $this->prepareLogMessage($action, $valueObject);
        if (null !== $message) {
            $this->getLogger()->notice($message);
        }
    }

    protected function prepareLogMessage(Action $action, ValueObject $valueObject): ?string
    {
        return null;
    }

    /**
     * @return string[]
     */
    private function getSupportedActions(): array
    {
        return array_keys($this->serviceLocator->getProvidedServices());
    }
}

class_alias(AbstractExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ActionExecutor\AbstractExecutor');
