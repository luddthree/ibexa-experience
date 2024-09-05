<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserGroupUpdateStep;
use Ibexa\Migration\ValueObject\UserGroup\Matcher;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

final class UserGroupUpdateStepExecutor extends AbstractUserGroupStepExecutor implements LoggerAwareInterface
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

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    protected $contentService;

    public function __construct(
        UserService $userService,
        ContentTypeService $contentTypeService,
        FieldTypeServiceInterface $fieldTypeService,
        RoleService $roleService,
        ExecutorInterface $actionExecutor,
        ContentService $contentService,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($roleService);

        $this->userService = $userService;
        $this->contentTypeService = $contentTypeService;
        $this->fieldTypeService = $fieldTypeService;
        $this->contentService = $contentService;
        $this->roleService = $roleService;
        $this->actionExecutor = $actionExecutor;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof UserGroupUpdateStep;
    }

    protected function doHandle(StepInterface $step): UserGroup
    {
        assert($step instanceof UserGroupUpdateStep);

        $matchField = $step->match->field;
        $matchValue = $step->match->value;
        switch ($matchField) {
            case Matcher::REMOTE_ID:
                $content = $this->contentService->loadContentByRemoteId((string) $matchValue);
                $userGroup = $this->userService->loadUserGroup($content->id);
                break;
            case Matcher::ID:
                $userGroup = $this->userService->loadUserGroup((int) $matchValue);
                break;
            default:
                throw new InvalidArgumentException(sprintf(
                    'Invalid Match provided. %s is unable to handle Match with field "%s"',
                    static::class,
                    $matchField
                ));
        }

        $userGroupUpdateStruct = $this->userService->newUserGroupUpdateStruct();
        $contentUpdateStruct = $this->contentService->newContentUpdateStruct();
        if ($step->metadata !== null) {
            $metadata = $step->metadata;

            $contentMetadataUpdateStruct = $this->contentService->newContentMetadataUpdateStruct();
            $contentMetadataUpdateStruct->remoteId = $metadata->remoteId;
            $contentMetadataUpdateStruct->alwaysAvailable = $metadata->alwaysAvailable;
            $contentMetadataUpdateStruct->mainLanguageCode = $metadata->mainLanguage;
            $contentMetadataUpdateStruct->modificationDate = $metadata->modificationDate;
            $contentMetadataUpdateStruct->ownerId = $metadata->ownerId;

            $userGroupUpdateStruct->contentMetadataUpdateStruct = $contentMetadataUpdateStruct;
        }

        $contentType = $userGroup->contentInfo->getContentType();
        foreach ($step->fields as $field) {
            $fieldDefinition = $contentType->getFieldDefinition($field->fieldDefIdentifier);

            if ($fieldDefinition === null) {
                continue;
            }

            $value = $this->fieldTypeService->getFieldValueFromHash(
                $field->value,
                $fieldDefinition->fieldTypeIdentifier,
                $fieldDefinition->getFieldSettings(),
            );

            $contentUpdateStruct->setField($field->fieldDefIdentifier, $value, $field->languageCode);
        }

        $userGroupUpdateStruct->contentUpdateStruct = $contentUpdateStruct;
        $userGroup = $this->userService->updateUserGroup($userGroup, $userGroupUpdateStruct);

        $this->assignRoleToUserGroup($userGroup, $step->roles);

        $this->getLogger()->notice(sprintf(
            'Update user group: "%s" (ID: %s)',
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
