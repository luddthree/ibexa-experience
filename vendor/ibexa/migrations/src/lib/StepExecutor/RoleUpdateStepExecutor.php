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
use Ibexa\Contracts\Core\Repository\Values\User\RoleDraft;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverterManager;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\Role\Matcher;
use Ibexa\Migration\ValueObject\Step\Role\PolicyList;
use Ibexa\Migration\ValueObject\Step\RoleUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function sprintf;
use Webmozart\Assert\Assert;

final class RoleUpdateStepExecutor extends AbstractStepExecutor implements LoggerAwareInterface
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
        return $step instanceof RoleUpdateStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\RoleUpdateStep $step
     *
     * @throws \Exception
     */
    public function doHandle(StepInterface $step): Role
    {
        $roleUpdateStruct = $this->roleService->newRoleUpdateStruct();
        if ($step->metadata->identifier !== null) {
            $roleUpdateStruct->identifier = $step->metadata->identifier;
        }

        switch ($step->match->field) {
            case Matcher::IDENTIFIER:
                $role = $this->roleService->loadRoleByIdentifier($step->match->value);
                break;
            case Matcher::ID:
                $role = $this->roleService->loadRole((int) $step->match->value);
                break;
            default:
                throw new InvalidArgumentException(sprintf('%s can only work with identifier matchers', __CLASS__));
        }

        $roleDraft = $this->roleService->createRoleDraft($role);

        $policyList = $step->getPolicyList();
        if ($policyList !== null) {
            if ($policyList->getMode() === PolicyList::MODE_REPLACE) {
                $this->removeExistingPolicies($roleDraft);
            }
            $this->addStepPolicies($policyList->getPolicies(), $roleDraft);
        }

        $roleDraft = $this->roleService->updateRoleDraft($roleDraft, $roleUpdateStruct);
        $this->roleService->publishRoleDraft($roleDraft);

        $this->getLogger()->notice(sprintf(
            'Updated role: "%s" (ID: %s)',
            $roleDraft->identifier,
            $roleDraft->id,
        ));

        return $role;
    }

    private function removeExistingPolicies(RoleDraft $roleDraft): void
    {
        foreach ($roleDraft->policies as $policy) {
            $this->roleService->removePolicyByRoleDraft($roleDraft, $policy);
        }
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Role\Policy[] $policies
     */
    private function addStepPolicies(array $policies, RoleDraft $roleDraft): void
    {
        foreach ($policies as $policy) {
            $policyCreateStruct = $this->roleService->newPolicyCreateStruct($policy->module, $policy->function);
            foreach ($policy->limitations ?? [] as $limitation) {
                $apiLimitation = $this->limitationConverter->convertMigrationToApi($limitation);
                $policyCreateStruct->addLimitation($apiLimitation);
            }
            $this->roleService->addPolicyByRoleDraft($roleDraft, $policyCreateStruct);
        }
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

class_alias(RoleUpdateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\RoleUpdateStepExecutor');
