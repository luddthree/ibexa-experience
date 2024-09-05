<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserGroupCreateStep;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

final class UserGroupCreateStepExecutor extends AbstractUserGroupStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    /** @var \Ibexa\Migration\Service\FieldTypeServiceInterface */
    private $fieldTypeService;

    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface */
    private $actionExecutor;

    public function __construct(
        UserService $userService,
        ContentTypeService $contentTypeService,
        FieldTypeServiceInterface $fieldTypeService,
        RoleService $roleService,
        ExecutorInterface $actionExecutor,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($roleService);

        $this->userService = $userService;
        $this->contentTypeService = $contentTypeService;
        $this->fieldTypeService = $fieldTypeService;
        $this->roleService = $roleService;
        $this->actionExecutor = $actionExecutor;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof UserGroupCreateStep;
    }

    protected function doHandle(StepInterface $step): UserGroup
    {
        assert($step instanceof UserGroupCreateStep);

        $contentType = null;
        if (isset($step->metadata->contentTypeIdentifier)) {
            $contentType = $this->contentTypeService->loadContentTypeByIdentifier($step->metadata->contentTypeIdentifier);
        }

        $userGroupCreateStruct = $this->userService->newUserGroupCreateStruct($step->metadata->mainLanguage, $contentType);
        $contentType = $userGroupCreateStruct->contentType;

        $userGroupCreateStruct->alwaysAvailable = $step->metadata->alwaysAvailable;
        $userGroupCreateStruct->modificationDate = $step->metadata->modificationDate;
        $userGroupCreateStruct->ownerId = $step->metadata->ownerId;
        $userGroupCreateStruct->remoteId = $step->metadata->remoteId;
        $userGroupCreateStruct->sectionId = $step->metadata->sectionId;

        foreach ($step->fields as $field) {
            $fieldDefinition = $contentType->getFieldDefinition($field->fieldDefIdentifier);

            $value = $this->fieldTypeService->getFieldValueFromHash(
                $field->value,
                $fieldDefinition->fieldTypeIdentifier,
                $fieldDefinition->getFieldSettings(),
            );

            $userGroupCreateStruct->setField($field->fieldDefIdentifier, $value, $field->languageCode);
        }

        $parentGroup = $this->userService->loadUserGroup($step->metadata->parentGroupId);

        $userGroup = $this->userService->createUserGroup($userGroupCreateStruct, $parentGroup);

        $this->assignRoleToUserGroup($userGroup, $step->roles);

        $this->getLogger()->notice(sprintf(
            'Added user group: "%s" (ID: %s)',
            $userGroup->getName(),
            $userGroup->id,
        ));

        return $userGroup;
    }

    protected function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        Assert::isInstanceOf($executionResult, UserGroup::class);

        foreach ($step->getActions() as $action) {
            $this->actionExecutor->handle($action, $executionResult);
        }
    }
}

class_alias(UserGroupCreateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\UserGroupCreateStepExecutor');
