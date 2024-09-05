<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use function array_map;
use function array_values;
use Exception;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use function is_int;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function sprintf;
use Webmozart\Assert\Assert;

final class ContentTypeCreateStepExecutor extends AbstractContentTypeStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface */
    private $actionExecutor;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var string */
    private $defaultUserLogin;

    /** @var int|null */
    private $defaultUserId;

    public function __construct(
        ExecutorInterface $actionExecutor,
        ContentTypeService $contentTypeService,
        FieldTypeServiceInterface $fieldTypeService,
        UserService $userService,
        string $defaultUserLogin,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($fieldTypeService);

        $this->actionExecutor = $actionExecutor;
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
        $this->defaultUserLogin = $defaultUserLogin;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ValueObject\Step\ContentTypeCreateStep;
    }

    protected function doHandle(StepInterface $step): ContentType
    {
        Assert::isInstanceOf($step, ValueObject\Step\ContentTypeCreateStep::class);

        $metadata = $step->getMetadata();
        $fieldsDefinitionCollection = $step->getFields();

        return $this->createFromValueObjects($metadata, $fieldsDefinitionCollection);
    }

    /**
     * @phpstan-param \Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection<
     *     \Ibexa\Migration\ValueObject\ContentType\FieldDefinition
     * > $collection
     */
    private function createFromValueObjects(
        ValueObject\ContentType\CreateMetadata $metadata,
        FieldDefinitionCollection $collection
    ): ContentType {
        $contentTypeCreateStruct = $this->contentTypeService->newContentTypeCreateStruct($metadata->identifier);
        $contentTypeCreateStruct->mainLanguageCode = $metadata->mainTranslation;
        $contentTypeCreateStruct->remoteId = $metadata->remoteId;
        $contentTypeCreateStruct->creatorId = $metadata->creatorId ?? $this->getDefaultUserId();
        $contentTypeCreateStruct->creationDate = $metadata->creationDate;
        $contentTypeCreateStruct->defaultAlwaysAvailable = $metadata->defaultAlwaysAvailable;
        $contentTypeCreateStruct->defaultSortField = $metadata->defaultSortField;
        $contentTypeCreateStruct->defaultSortOrder = $metadata->defaultSortOrder;
        $contentTypeCreateStruct->isContainer = $metadata->container;
        $contentTypeCreateStruct->nameSchema = $metadata->nameSchema;
        $contentTypeCreateStruct->urlAliasSchema = $metadata->urlAliasSchema;

        [
            $contentTypeCreateStruct->names,
            $contentTypeCreateStruct->descriptions
        ] = $this->transformTranslations($metadata->translations);

        $contentTypeGroups = $this->prepareContentTypeGroups($metadata->contentTypeGroups);

        $contentTypeDraft = $this->contentTypeService->createContentType(
            $contentTypeCreateStruct,
            $contentTypeGroups
        );

        foreach ($collection as $fieldDefinition) {
            $this->addFieldDefinition($fieldDefinition, $contentTypeDraft);
        }

        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeDraft->identifier);

        $this->getLogger()->notice(sprintf(
            'Added content type: "%s" (ID: %s)',
            $contentType->identifier,
            $contentType->id,
        ));

        return $contentType;
    }

    private function addFieldDefinition(
        ValueObject\ContentType\FieldDefinition $fieldDefinition,
        ContentTypeDraft $contentTypeDraft
    ): void {
        try {
            $fieldTypeIdentifier = $fieldDefinition->getType();
            $fieldDefinitionCreateStruct = $this->contentTypeService->newFieldDefinitionCreateStruct(
                $fieldDefinition->getIdentifier(),
                $fieldTypeIdentifier
            );

            [
                $fieldDefinitionCreateStruct->names,
                $fieldDefinitionCreateStruct->descriptions
            ] = $this->transformTranslations($fieldDefinition->getTranslations());

            $fieldDefinitionCreateStruct->defaultValue = $this->prepareFieldDefaultValue($fieldDefinition);
            $fieldDefinitionCreateStruct->fieldSettings = $this->fieldTypeService->getFieldSettingsFromHash(
                $fieldDefinition->getFieldSettings(),
                $fieldTypeIdentifier
            );
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
            throw new \RuntimeException(
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

    /**
     * @param array<string|int> $contentTypeGroups
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup[]
     */
    private function prepareContentTypeGroups(array $contentTypeGroups): array
    {
        Assert::minCount($contentTypeGroups, 1, 'contentTypeGroups should have at least one element');

        return array_values(
            array_map(
                function ($group): ContentTypeGroup {
                    return is_int($group) ?
                        $this->contentTypeService->loadContentTypeGroup($group) :
                        $this->contentTypeService->loadContentTypeGroupByIdentifier($group);
                },
                $contentTypeGroups
            )
        );
    }

    private function getDefaultUserId(): int
    {
        if (!isset($this->defaultUserId)) {
            $this->defaultUserId = $this->userService->loadUserByLogin($this->defaultUserLogin)->id;
        }

        return $this->defaultUserId;
    }

    public function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        Assert::isInstanceOf($executionResult, ContentType::class);

        foreach ($step->getActions() as $action) {
            $this->actionExecutor->handle($action, $executionResult);
        }
    }
}

class_alias(ContentTypeCreateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ContentTypeCreateStepExecutor');
