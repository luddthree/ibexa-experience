<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Exception;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition as ApiFieldDefinition;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderRegistryInterface;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinition;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\ValueObject\ContentType\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\ContentTypeUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use Webmozart\Assert\Assert;

final class ContentTypeUpdateStepExecutor extends AbstractContentTypeStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface */
    private $actionExecutor;

    /** @var \Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderRegistryInterface */
    private $contentTypeFinderRegistry;

    /** @var string */
    private $defaultUserLogin;

    /** @var int|null */
    private $defaultUserId;

    public function __construct(
        UserService $userService,
        ContentTypeService $contentTypeService,
        FieldTypeServiceInterface $fieldTypeService,
        ExecutorInterface $actionExecutor,
        ContentTypeFinderRegistryInterface $contentTypeFinderRegistry,
        string $defaultUserLogin,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($fieldTypeService);

        $this->userService = $userService;
        $this->contentTypeService = $contentTypeService;
        $this->actionExecutor = $actionExecutor;
        $this->defaultUserLogin = $defaultUserLogin;
        $this->contentTypeFinderRegistry = $contentTypeFinderRegistry;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ContentTypeUpdateStep;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    protected function doHandle(StepInterface $step): ContentType
    {
        /** @var \Ibexa\Migration\ValueObject\Step\ContentTypeUpdateStep $step */
        Assert::isInstanceOf($step, ContentTypeUpdateStep::class);

        $metadata = $step->getMetadata();
        $matcher = $step->getMatch();
        $fieldsDefinitionCollection = $step->getFields();

        $finder = $this->contentTypeFinderRegistry->getFinder($matcher->getField());
        $contentType = $finder->find($matcher);

        if ($metadata->modifierId === null) {
            if (!isset($this->defaultUserId)) {
                $user = $this->userService->loadUserByLogin($this->defaultUserLogin);
                $this->defaultUserId = $user->id;
            }

            $metadata->modifierId = $this->defaultUserId;
        }

        return $this->updateFromValueObjects($contentType, $metadata, $fieldsDefinitionCollection);
    }

    private function updateFromValueObjects(
        ContentType $contentType,
        UpdateMetadata $metadata,
        FieldDefinitionCollection $collection
    ): ContentType {
        $contentTypeDraft = $this->tryToCreateContentTypeDraft($contentType);
        $apiFieldDefinitionCollection = $contentType->getFieldDefinitions();

        $contentTypeUpdateStruct = $this->contentTypeService->newContentTypeUpdateStruct();
        $contentTypeUpdateStruct->modifierId = $metadata->modifierId;
        $contentTypeUpdateStruct->modificationDate = $metadata->modificationDate;
        $contentTypeUpdateStruct->identifier = $metadata->identifier;
        $contentTypeUpdateStruct->mainLanguageCode = $metadata->mainTranslation;
        $contentTypeUpdateStruct->remoteId = $metadata->remoteId;
        $contentTypeUpdateStruct->defaultAlwaysAvailable = $metadata->defaultAlwaysAvailable;
        $contentTypeUpdateStruct->defaultSortField = $metadata->defaultSortField;
        $contentTypeUpdateStruct->defaultSortOrder = $metadata->defaultSortOrder;
        $contentTypeUpdateStruct->isContainer = $metadata->container;
        $contentTypeUpdateStruct->nameSchema = $metadata->nameSchema;
        $contentTypeUpdateStruct->urlAliasSchema = $metadata->urlAliasSchema;
        [
            $contentTypeUpdateStruct->names,
            $contentTypeUpdateStruct->descriptions
        ] = $this->transformTranslations($metadata->translations);

        $this->contentTypeService->updateContentTypeDraft($contentTypeDraft, $contentTypeUpdateStruct);

        foreach ($collection as $fieldDefinition) {
            /** @var $fieldDefinition \Ibexa\Migration\ValueObject\ContentType\FieldDefinition */
            if ($apiFieldDefinitionCollection->has($fieldDefinition->identifier)) {
                $apiFieldDefinition = $apiFieldDefinitionCollection->get($fieldDefinition->identifier);

                $this->guardFieldTypeIsUnchanged($fieldDefinition, $apiFieldDefinition);
                $this->updateFieldDefinition($apiFieldDefinition, $fieldDefinition, $contentTypeDraft);
            } else {
                $this->addFieldDefinition($fieldDefinition, $contentTypeDraft);
            }
        }

        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $this->getLogger()->notice(sprintf('Updated content type: %s', $metadata->identifier));

        return $contentType;
    }

    private function tryToCreateContentTypeDraft(ContentType $contentType): ContentTypeDraft
    {
        try {
            $contentTypeDraft = $this->contentTypeService->loadContentTypeDraft($contentType->id, true);
            $this->contentTypeService->deleteContentType($contentTypeDraft);
        } catch (NotFoundException $e) {
        } finally {
            return $this->contentTypeService->createContentTypeDraft($contentType);
        }
    }

    private function updateFieldDefinition(
        ApiFieldDefinition $apiFieldDefinition,
        FieldDefinition $fieldDefinition,
        ContentTypeDraft $contentTypeDraft
    ): void {
        try {
            $fieldDefinitionCreateStruct = $this->contentTypeService->newFieldDefinitionUpdateStruct();

            [
                $fieldDefinitionCreateStruct->names,
                $fieldDefinitionCreateStruct->descriptions
            ] = $this->transformTranslations($fieldDefinition->getTranslations());

            $fieldSettings = $fieldDefinition->getFieldSettings() ?? $apiFieldDefinition->getFieldSettings();
            $fieldDefinitionCreateStruct->defaultValue = $this->prepareFieldDefaultValue($fieldDefinition, $fieldSettings);
            $fieldDefinitionCreateStruct->fieldSettings = $fieldSettings;
            $fieldDefinitionCreateStruct->validatorConfiguration = $fieldDefinition->getValidatorConfiguration() ?? $apiFieldDefinition->getValidatorConfiguration();
            if ($fieldDefinition->getPosition() !== null) {
                $fieldDefinitionCreateStruct->position = $fieldDefinition->getPosition();
            }
            if ($fieldDefinition->getCategory() !== null) {
                $fieldDefinitionCreateStruct->fieldGroup = $fieldDefinition->getCategory();
            }
            if ($fieldDefinition->isTranslatable() !== null) {
                $fieldDefinitionCreateStruct->isTranslatable = $fieldDefinition->isTranslatable();
            }
            if ($fieldDefinition->isThumbnail() !== null) {
                $fieldDefinitionCreateStruct->isThumbnail = $fieldDefinition->isThumbnail();
            }
            if ($fieldDefinition->isInfoCollector() !== null) {
                $fieldDefinitionCreateStruct->isInfoCollector = $fieldDefinition->isInfoCollector();
            }
            if ($fieldDefinition->isSearchable() !== null) {
                $fieldDefinitionCreateStruct->isSearchable = $fieldDefinition->isSearchable();
            }
            if ($fieldDefinition->isRequired() !== null) {
                $fieldDefinitionCreateStruct->isRequired = $fieldDefinition->isRequired();
            }
            if ($fieldDefinition->getNewIdentifier() !== null) {
                $fieldDefinitionCreateStruct->identifier = $fieldDefinition->getNewIdentifier();
            }

            $this->contentTypeService->updateFieldDefinition(
                $contentTypeDraft,
                $apiFieldDefinition,
                $fieldDefinitionCreateStruct
            );
        } catch (Exception $e) {
            throw new RuntimeException(
                sprintf(
                    'Unable to update field `%s` to content type `%s`',
                    $fieldDefinition->getIdentifier(),
                    $contentTypeDraft->identifier
                ),
                0,
                $e
            );
        }
    }

    private function addFieldDefinition(
        FieldDefinition $fieldDefinition,
        ContentTypeDraft $contentTypeDraft
    ): void {
        try {
            $fieldDefinitionCreateStruct = $this->contentTypeService->newFieldDefinitionCreateStruct(
                $fieldDefinition->getIdentifier(),
                $fieldDefinition->getType()
            );

            [
                $fieldDefinitionCreateStruct->names,
                $fieldDefinitionCreateStruct->descriptions
            ] = $this->transformTranslations($fieldDefinition->getTranslations());

            $fieldDefinitionCreateStruct->defaultValue = $this->prepareFieldDefaultValue($fieldDefinition);
            $fieldDefinitionCreateStruct->fieldSettings = $fieldDefinition->getFieldSettings();
            $fieldDefinitionCreateStruct->validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
            $fieldDefinitionCreateStruct->position = $fieldDefinition->getPosition();
            $fieldDefinitionCreateStruct->fieldGroup = $fieldDefinition->getCategory();
            $fieldDefinitionCreateStruct->isTranslatable = $fieldDefinition->isTranslatable();
            $fieldDefinitionCreateStruct->isThumbnail = $fieldDefinition->isThumbnail();
            $fieldDefinitionCreateStruct->isInfoCollector = $fieldDefinition->isInfoCollector();
            $fieldDefinitionCreateStruct->isSearchable = $fieldDefinition->isSearchable();
            $fieldDefinitionCreateStruct->isRequired = $fieldDefinition->isRequired();

            $this->contentTypeService->addFieldDefinition(
                $contentTypeDraft,
                $fieldDefinitionCreateStruct
            );
        } catch (Exception $e) {
            throw new RuntimeException(
                sprintf(
                    'Unable to add field `%s` to content type `%s`',
                    $fieldDefinition->getIdentifier(),
                    $contentTypeDraft->identifier
                ),
                0,
                $e
            );
        }
    }

    public function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        Assert::isInstanceOf($executionResult, ContentType::class);

        foreach ($step->getActions() as $action) {
            $this->actionExecutor->handle($action, $executionResult);
        }
    }

    private function guardFieldTypeIsUnchanged(FieldDefinition $fieldDefinition, ApiFieldDefinition $apiFieldDefinition): void
    {
        if ($fieldDefinition->type !== $apiFieldDefinition->fieldTypeIdentifier) {
            throw new RuntimeException(
                'Field definition type cannot be updated. '
                . 'In order to change field definition type remove field with old type and add a new one.'
            );
        }
    }
}

class_alias(ContentTypeUpdateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ContentTypeUpdateStepExecutor');
