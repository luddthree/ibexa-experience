<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use function array_map;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserCreateStep;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

final class UserCreateStepExecutor extends AbstractStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Persistence\TransactionHandler */
    private $transactionHandler;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Migration\Service\FieldTypeServiceInterface */
    private $fieldTypeService;

    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface */
    private $actionExecutor;

    public function __construct(
        UserService $userService,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        FieldTypeServiceInterface $fieldTypeService,
        ExecutorInterface $actionExecutor,
        ?LoggerInterface $logger = null
    ) {
        $this->userService = $userService;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->fieldTypeService = $fieldTypeService;
        $this->actionExecutor = $actionExecutor;
        $this->logger = $logger;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof UserCreateStep;
    }

    protected function doHandle(StepInterface $step): User
    {
        assert($step instanceof UserCreateStep);

        $contentType = null;
        if (null !== $step->metadata->contentType) {
            $contentType = $this->contentTypeService->loadContentTypeByIdentifier($step->metadata->contentType);
        }

        $userCreateStruct = $this->userService->newUserCreateStruct(
            $step->metadata->login,
            $step->metadata->email,
            $step->metadata->password,
            $step->metadata->mainLanguage,
            $contentType
        );
        $contentType = $userCreateStruct->contentType;

        foreach ($step->fields as $field) {
            $fieldDefinition = $contentType->getFieldDefinition($field->fieldDefIdentifier);

            $value = $this->fieldTypeService->getFieldValueFromHash(
                $field->value,
                $fieldDefinition->fieldTypeIdentifier,
                $fieldDefinition->getFieldSettings(),
            );

            $userCreateStruct->setField($field->fieldDefIdentifier, $value, $field->languageCode);
        }

        $parentGroups = array_map(function (string $groupIdentifier): UserGroup {
            return $this->loadUserGroup($groupIdentifier);
        }, $step->groups);

        $user = $this->userService->createUser($userCreateStruct, $parentGroups);

        $this->getLogger()->notice(sprintf(
            'Added user: "%s" (ID: %s)',
            $user->getName(),
            $user->id,
        ));

        return $user;
    }

    private function loadUserGroup(string $groupIdentifier): UserGroup
    {
        if (is_numeric($groupIdentifier)) {
            $userGroupId = (int) $groupIdentifier;
        } else {
            $content = $this->contentService->loadContentByRemoteId($groupIdentifier);
            if (!$this->userService->isUserGroup($content)) {
                throw new InvalidArgumentException(
                    sprintf('Remote ID provided ("%s") does not match to User Group instance.', $groupIdentifier)
                );
            }
            $userGroupId = $content->id;
        }

        return $this->userService->loadUserGroup($userGroupId);
    }

    protected function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        Assert::isInstanceOf($executionResult, User::class);

        foreach ($step->getActions() as $action) {
            $this->actionExecutor->handle($action, $executionResult);
        }
    }

    private function getLogger(): LoggerInterface
    {
        if (!isset($this->logger)) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }
}

class_alias(UserCreateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\UserCreateStepExecutor');
