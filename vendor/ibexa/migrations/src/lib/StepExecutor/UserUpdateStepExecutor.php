<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter\ContentFilteringAdapter;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Migration\ValueObject\Step\UserUpdateStep;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use Webmozart\Assert\Assert;

final class UserUpdateStepExecutor extends AbstractStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Migration\Service\FieldTypeServiceInterface */
    private $fieldTypeService;

    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface */
    private $actionExecutor;

    public function __construct(
        UserService $userService,
        ContentService $contentService,
        FieldTypeServiceInterface $fieldTypeService,
        ExecutorInterface $actionExecutor,
        ?LoggerInterface $logger = null
    ) {
        $this->userService = $userService;
        $this->contentService = $contentService;
        $this->fieldTypeService = $fieldTypeService;
        $this->actionExecutor = $actionExecutor;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof UserUpdateStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\StepInterface|\Ibexa\Migration\ValueObject\Step\UserUpdateStep $step
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User[]
     */
    protected function doHandle(StepInterface $step): array
    {
        Assert::isInstanceOf($step, UserUpdateStep::class);

        $filter = new Filter();
        $filter->andWithCriterion(new Criterion\ContentTypeIdentifier('user'));
        $filter->andWithCriterion($step->criterion);
        $filter->withSortClause(new Query\SortClause\ContentId());

        /** @var \Iterator|\Ibexa\Contracts\Core\Repository\Values\Content\Content[] $contentItems */
        $contentItems = new BatchIterator(new ContentFilteringAdapter($this->contentService, $filter));

        $modifiedUsers = [];
        foreach ($contentItems as $content) {
            $user = $this->userService->loadUser($content->id);
            Assert::isInstanceOf($user, User::class);

            $modifiedUsers[] = $this->updateUser($user, $step);
        }

        return $modifiedUsers;
    }

    protected function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        Assert::isArray($executionResult);
        Assert::allIsInstanceOf($executionResult, User::class);

        foreach ($executionResult as $user) {
            foreach ($step->getActions() as $action) {
                $this->actionExecutor->handle($action, $user);
            }
        }
    }

    private function updateUser(User $user, UserUpdateStep $step): User
    {
        $contentType = $user->getContentType();

        $userUpdateStruct = $this->userService->newUserUpdateStruct();
        $userUpdateStruct->enabled = $step->metadata->enabled;
        $userUpdateStruct->email = $step->metadata->email;
        $userUpdateStruct->password = $step->metadata->password;

        foreach ($step->fields as $field) {
            if (!isset($userUpdateStruct->contentUpdateStruct)) {
                $userUpdateStruct->contentUpdateStruct = $this->contentService->newContentUpdateStruct();
            }

            $fieldDefinition = $contentType->getFieldDefinition($field->fieldDefIdentifier);

            if ($fieldDefinition === null) {
                $existingFieldDefinitions = $contentType->getFieldDefinitions()->map(
                    static function (FieldDefinition $fieldDefinition): string {
                        return $fieldDefinition->identifier;
                    }
                );

                throw new RuntimeException(sprintf(
                    'Content type "%s" (identifier: "%s") does not contain field with identifier "%s". Available fields: "%s"',
                    $contentType->identifier,
                    $contentType->getName(),
                    $field->fieldDefIdentifier,
                    implode('", "', $existingFieldDefinitions),
                ));
            }

            $value = $this->fieldTypeService->getFieldValueFromHash(
                $field->value,
                $fieldDefinition->fieldTypeIdentifier,
                $fieldDefinition->getFieldSettings(),
            );

            $userUpdateStruct->contentUpdateStruct->setField($field->fieldDefIdentifier, $value, $field->languageCode);
        }

        $user = $this->userService->updateUser($user, $userUpdateStruct);

        $this->getLogger()->notice(sprintf(
            'Updated user: "%s" (ID: %s)',
            $user->getName(),
            $user->id,
        ));

        return $user;
    }
}

class_alias(UserUpdateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\UserUpdateStepExecutor');
