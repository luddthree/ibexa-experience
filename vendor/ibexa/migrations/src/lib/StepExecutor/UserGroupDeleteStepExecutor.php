<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Exception;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserGroupDeleteStep;
use Ibexa\Migration\ValueObject\UserGroup\Matcher;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function sprintf;
use Webmozart\Assert\Assert;

final class UserGroupDeleteStepExecutor implements StepExecutorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Persistence\TransactionHandler */
    private $transactionHandler;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    public function __construct(
        TransactionHandler $transactionHandler,
        ContentService $contentService,
        UserService $userService,
        ?LoggerInterface $logger = null
    ) {
        $this->transactionHandler = $transactionHandler;
        $this->contentService = $contentService;
        $this->userService = $userService;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof UserGroupDeleteStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\StepInterface|\Ibexa\Migration\ValueObject\Step\UserGroupDeleteStep $step
     *
     * @throws \Exception
     */
    public function handle(StepInterface $step): void
    {
        Assert::isInstanceOf($step, UserGroupDeleteStep::class);

        $this->transactionHandler->beginTransaction();

        try {
            $userGroup = $this->findMatchingUserGroup($step->match);
            $name = $userGroup->getName();

            $this->userService->deleteUserGroup($userGroup);

            $this->getLogger()->notice(sprintf(
                'Removed user group: "%s" (ID: %s)',
                $name,
                $userGroup->id,
            ));

            $this->transactionHandler->commit();
        } catch (Exception $e) {
            $this->transactionHandler->rollback();
            throw $e;
        }
    }

    private function findMatchingUserGroup(Matcher $match): UserGroup
    {
        switch ($match->field) {
            case Matcher::REMOTE_ID:
                $content = $this->contentService->loadContentByRemoteId($match->value);
                $userGroup = $this->userService->loadUserGroup($content->id);
                break;
            case Matcher::ID:
                $userGroup = $this->userService->loadUserGroup((int) $match->value);
                break;
            default:
                throw new InvalidArgumentException(sprintf(
                    'Invalid Match provided. %s is unable to handle Match with field "%s"',
                    static::class,
                    $match->field
                ));
        }

        return $userGroup;
    }
}

class_alias(UserGroupDeleteStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\UserGroupDeleteStepExecutor');
