<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Exception;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Step\Role\Matcher;
use Ibexa\Migration\ValueObject\Step\RoleDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function sprintf;

final class RoleDeleteStepExecutor implements StepExecutorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Persistence\TransactionHandler */
    private $transactionHandler;

    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    public function __construct(
        TransactionHandler $transactionHandler,
        RoleService $roleService,
        ?LoggerInterface $logger = null
    ) {
        $this->transactionHandler = $transactionHandler;
        $this->roleService = $roleService;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof RoleDeleteStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\RoleDeleteStep $step
     *
     * @throws \Exception
     */
    public function handle(StepInterface $step): void
    {
        $this->transactionHandler->beginTransaction();
        try {
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

            $this->roleService->deleteRole($role);

            $this->getLogger()->notice(sprintf(
                'Removed role: "%s" (ID: %s)',
                $role->identifier,
                $role->id,
            ));

            $this->transactionHandler->commit();
        } catch (Exception $e) {
            $this->transactionHandler->rollback();
            throw $e;
        }
    }
}

class_alias(RoleDeleteStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\RoleDeleteStepExecutor');
