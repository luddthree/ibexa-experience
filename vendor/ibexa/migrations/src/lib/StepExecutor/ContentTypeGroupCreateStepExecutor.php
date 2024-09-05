<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use DateTime;
use Exception;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function sprintf;
use Webmozart\Assert\Assert;

final class ContentTypeGroupCreateStepExecutor implements LoggerAwareInterface, StepExecutorInterface, UserContextAwareStepExecutorInterface
{
    use LoggerAwareTrait;
    use UserContextAwareStepExecutorTrait;

    /** @var \Ibexa\Contracts\Core\Persistence\TransactionHandler */
    private $transactionHandler;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var string */
    private $defaultUserLogin;

    /** @var int|null */
    private $defaultUserId;

    public function __construct(
        TransactionHandler $transactionHandler,
        ContentTypeService $contentTypeService,
        UserService $userService,
        string $defaultUserLogin,
        ?LoggerInterface $logger = null
    ) {
        $this->transactionHandler = $transactionHandler;
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
        $this->defaultUserLogin = $defaultUserLogin;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ValueObject\Step\ContentTypeGroupCreateStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\StepInterface|\Ibexa\Migration\ValueObject\Step\ContentTypeGroupCreateStep $step
     */
    public function handle(StepInterface $step): void
    {
        Assert::isInstanceOf($step, ValueObject\Step\ContentTypeGroupCreateStep::class);

        $metadata = $step->metadata;

        $this->transactionHandler->beginTransaction();

        try {
            $this->loginApiUser($metadata->creatorId ?? $this->getDefaultUserId());
            $this->createFromValueObjects($metadata);
            $this->restorePreviousApiUser();

            $this->transactionHandler->commit();
        } catch (Exception $e) {
            $this->transactionHandler->rollback();
            throw $e;
        }
    }

    private function createFromValueObjects(
        ValueObject\ContentTypeGroup\CreateMetadata $metadata
    ): void {
        $contentTypeGroupCreateStruct = $this->contentTypeService->newContentTypeGroupCreateStruct($metadata->identifier);
        $contentTypeGroupCreateStruct->creatorId = $metadata->creatorId ?? $this->getDefaultUserId();
        $contentTypeGroupCreateStruct->creationDate = new DateTime($metadata->creationDate);
        $contentTypeGroupCreateStruct->isSystem = $metadata->isSystem;

        $contentTypeGroup = $this->contentTypeService->createContentTypeGroup($contentTypeGroupCreateStruct);

        $this->getLogger()->notice(sprintf(
            'Added content type group: "%s" (ID: %s)',
            $contentTypeGroup->identifier,
            $contentTypeGroup->id,
        ));
    }

    private function getDefaultUserId(): int
    {
        return $this->defaultUserId ?? $this->defaultUserId = $this->userService->loadUserByLogin($this->defaultUserLogin)->id;
    }
}

class_alias(ContentTypeGroupCreateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ContentTypeGroupCreateStepExecutor');
