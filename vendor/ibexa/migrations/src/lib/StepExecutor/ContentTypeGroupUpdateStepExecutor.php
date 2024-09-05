<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use DateTime;
use Exception;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function sprintf;
use Webmozart\Assert\Assert;

final class ContentTypeGroupUpdateStepExecutor implements LoggerAwareInterface, StepExecutorInterface, UserContextAwareStepExecutorInterface
{
    use LoggerAwareTrait;
    use UserContextAwareStepExecutorTrait;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserReference|null */
    private $previousApiUser;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var string */
    private $defaultUserLogin;

    /** @var int|null */
    private $defaultUserId;

    public function __construct(
        Repository $repository,
        ContentTypeService $contentTypeService,
        UserService $userService,
        string $defaultUserLogin,
        ?LoggerInterface $logger = null
    ) {
        $this->repository = $repository;
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
        $this->defaultUserLogin = $defaultUserLogin;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ValueObject\Step\ContentTypeGroupUpdateStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\StepInterface|\Ibexa\Migration\ValueObject\Step\ContentTypeGroupUpdateStep $step
     */
    public function handle(StepInterface $step): void
    {
        Assert::isInstanceOf($step, ValueObject\Step\ContentTypeGroupUpdateStep::class);

        $metadata = $step->metadata;
        $matcher = $step->match;

        Assert::eq(ValueObject\ContentTypeGroup\Matcher::CONTENT_TYPE_NAME_IDENTIFIER, $matcher->field);

        $this->repository->beginTransaction();

        try {
            $this->loginApiUser($metadata->modifierId ?? $this->getDefaultUserId());
            $this->createFromValueObjects($metadata, $matcher);
            $this->restorePreviousApiUser();

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    private function createFromValueObjects(
        ValueObject\ContentTypeGroup\UpdateMetadata $metadata,
        ValueObject\ContentTypeGroup\Matcher $matcher
    ): void {
        $contentTypeGroupUpdateStruct = $this->contentTypeService->newContentTypeGroupUpdateStruct();
        $contentTypeGroupUpdateStruct->modifierId = $metadata->modifierId ?? $this->getDefaultUserId();
        $contentTypeGroupUpdateStruct->modificationDate = new DateTime($metadata->modificationDate);
        $contentTypeGroupUpdateStruct->identifier = $metadata->identifier;
        $contentTypeGroupUpdateStruct->isSystem = $metadata->isSystem;

        $contentTypeGroup = $this->contentTypeService->loadContentTypeGroupByIdentifier($matcher->value);

        $this->contentTypeService->updateContentTypeGroup(
            $contentTypeGroup,
            $contentTypeGroupUpdateStruct
        );

        $this->getLogger()->notice(sprintf(
            'Updated content type group: "%s" (ID: %s)',
            $contentTypeGroup->identifier,
            $contentTypeGroup->id,
        ));
    }

    private function getDefaultUserId(): int
    {
        return $this->defaultUserId ?? $this->defaultUserId = $this->userService->loadUserByLogin($this->defaultUserLogin)->id;
    }
}

class_alias(ContentTypeGroupUpdateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ContentTypeGroupUpdateStepExecutor');
