<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration\StepExecutor;

use Exception;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\StepExecutor\StepExecutorInterface;
use Ibexa\Migration\StepExecutor\UserContextAwareStepExecutorInterface;
use Ibexa\Migration\StepExecutor\UserContextAwareStepExecutorTrait;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\ReferenceAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserContextAwareStepInterface;
use LogicException;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

/**
 * Common class for Step executors.
 */
abstract class AbstractStepExecutor implements StepExecutorInterface, UserContextAwareStepExecutorInterface, ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;
    use UserContextAwareStepExecutorTrait;

    final public function handle(StepInterface $step): void
    {
        try {
            $this->transactionHandler()->beginTransaction();

            if ($step instanceof UserContextAwareStepInterface) {
                $userId = $step->getUserId();
                $this->loginApiUser($userId);
            }

            $executionResult = $this->doHandle($step);

            if ($step instanceof ActionsAwareStepInterface) {
                $this->handleActions($step, $executionResult);
            }

            if ($step instanceof UserContextAwareStepInterface) {
                $this->restorePreviousApiUser();
            }

            $this->transactionHandler()->commit();
        } catch (Exception $e) {
            $this->transactionHandler()->rollback();
            throw $e;
        }

        if ($step instanceof ReferenceAwareStepInterface) {
            $this->doCollectReferences($step, $executionResult);
        }
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\ValueObject[]|null
     */
    abstract protected function doHandle(StepInterface $step);

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ReferenceAwareStepInterface $step
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\ValueObject[]|null $executionResults
     */
    final protected function doCollectReferences(ReferenceAwareStepInterface $step, $executionResults): void
    {
        if ($executionResults === null) {
            return;
        }

        if (!is_array($executionResults)) {
            $executionResults = [$executionResults];
        }

        foreach ($executionResults as $valueObject) {
            foreach ($step->getReferences() as $referenceDefinition) {
                $reference = $this->referenceResolver()->resolve($referenceDefinition, $valueObject);
                $this->referenceCollector()->collect($reference);
            }
        }
    }

    /**
     * @return array<string>|array<string, string>
     */
    public static function getSubscribedServices()
    {
        return [
            TransactionHandler::class,
            '?' . ResolverInterface::class,
            CollectorInterface::class,
        ];
    }

    private function transactionHandler(): TransactionHandler
    {
        return $this->container->get(TransactionHandler::class);
    }

    private function referenceResolver(): ResolverInterface
    {
        if (!$this->container->has(ResolverInterface::class)) {
            throw new LogicException(sprintf(
                'Missing "%s" service, required to handle reference resolution during migration.',
                ResolverInterface::class,
            ));
        }

        return $this->container->get(ResolverInterface::class);
    }

    private function referenceCollector(): CollectorInterface
    {
        return $this->container->get(CollectorInterface::class);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\ValueObject[]|null $executionResult
     */
    protected function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        throw new LogicException(sprintf(
            '%s received an instance of %s (%s). Unable to handle actions - missing implementation.',
            static::class,
            ActionsAwareStepInterface::class,
            get_class($step),
        ));
    }
}

class_alias(AbstractStepExecutor::class, 'Ibexa\Platform\Contracts\Migration\StepExecutor\AbstractStepExecutor');
