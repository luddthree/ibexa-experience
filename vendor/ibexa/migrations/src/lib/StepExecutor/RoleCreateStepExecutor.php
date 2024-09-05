<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverterManager;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\RoleCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

final class RoleCreateStepExecutor extends AbstractStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Persistence\TransactionHandler */
    private $transactionHandler;

    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface */
    private $actionExecutor;

    /** @var \Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverterManager */
    private $limitationConverter;

    public function __construct(
        TransactionHandler $transactionHandler,
        RoleService $roleService,
        LimitationConverterManager $limitationConverter,
        ActionExecutor\ExecutorInterface $actionExecutor,
        ?LoggerInterface $logger = null
    ) {
        $this->transactionHandler = $transactionHandler;
        $this->roleService = $roleService;
        $this->limitationConverter = $limitationConverter;
        $this->actionExecutor = $actionExecutor;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof RoleCreateStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\StepInterface|\Ibexa\Migration\ValueObject\Step\RoleCreateStep $step
     *
     * @throws \Exception
     */
    public function doHandle(StepInterface $step): Role
    {
        Assert::isInstanceOf($step, RoleCreateStep::class);

        $roleCreateStruct = $this->roleService->newRoleCreateStruct($step->metadata->identifier);

        foreach ($step->policies as $policy) {
            $policyCreateStruct = $this->roleService->newPolicyCreateStruct($policy->module, $policy->function);
            foreach ($policy->limitations as $limitation) {
                $apiLimitation = $this->limitationConverter->convertMigrationToApi($limitation);
                $policyCreateStruct->addLimitation($apiLimitation);
            }
            $roleCreateStruct->addPolicy($policyCreateStruct);
        }

        $roleDraft = $this->roleService->createRole($roleCreateStruct);
        $this->roleService->publishRoleDraft($roleDraft);
        $role = $this->roleService->loadRoleByIdentifier($roleDraft->identifier);

        $this->getLogger()->notice(sprintf(
            'Added role: "%s" (ID: %s)',
            $role->identifier,
            $role->id,
        ));

        return $role;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\ValueObject[]|null $executionResult
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        Assert::isInstanceOf($executionResult, Role::class);

        foreach ($step->getActions() as $action) {
            $this->actionExecutor->handle($action, $executionResult);
        }
    }
}

class_alias(RoleCreateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\RoleCreateStepExecutor');
