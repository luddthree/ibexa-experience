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
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\ContentUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

final class ContentUpdateStepExecutor extends AbstractStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Migration\Service\FieldTypeServiceInterface */
    private $fieldTypeService;

    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface */
    private $actionExecutor;

    public function __construct(
        ContentService $contentService,
        FieldTypeServiceInterface $fieldTypeService,
        ExecutorInterface $actionExecutor,
        ?LoggerInterface $logger = null
    ) {
        $this->contentService = $contentService;
        $this->fieldTypeService = $fieldTypeService;
        $this->actionExecutor = $actionExecutor;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ContentUpdateStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ContentUpdateStep $step
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content[]
     */
    protected function doHandle(StepInterface $step): array
    {
        $filter = new Filter();
        $filter->andWithCriterion($step->criterion);
        $filter->withSortClause(new Query\SortClause\ContentId());

        /** @var \Iterator|\Ibexa\Contracts\Core\Repository\Values\Content\Content[] $contentItems */
        $contentItems = new BatchIterator(new ContentFilteringAdapter($this->contentService, $filter));

        $updatedContentItems = [];
        foreach ($contentItems as $content) {
            $contentType = $content->getContentType();
            $contentDraft = $this->contentService->createContentDraft($content->contentInfo);

            $contentUpdateStruct = $this->contentService->newContentUpdateStruct();
            if ($step->metadata->initialLanguageCode !== null) {
                $contentUpdateStruct->initialLanguageCode = $step->metadata->initialLanguageCode;
            }
            $contentUpdateStruct->creatorId = $step->metadata->creatorId;

            foreach ($step->fields as $field) {
                $fieldDefinition = $contentType->getFieldDefinition($field->fieldDefIdentifier);

                if ($fieldDefinition === null) {
                    throw new InvalidArgumentException(sprintf(
                        'Missing field definition with identifier: "%s" for "%s" content type',
                        $field->fieldDefIdentifier,
                        $contentType->identifier,
                    ));
                }

                $value = $this->fieldTypeService->getFieldValueFromHash(
                    $field->value,
                    $fieldDefinition->fieldTypeIdentifier,
                    $fieldDefinition->getFieldSettings(),
                );

                $contentUpdateStruct->setField($field->fieldDefIdentifier, $value, $field->languageCode);
            }

            if ($step->metadata->requiresContentMetadataUpdate()) {
                $contentMetadataUpdateStruct = $this->contentService->newContentMetadataUpdateStruct();
                $contentMetadataUpdateStruct->remoteId = $step->metadata->remoteId;
                $contentMetadataUpdateStruct->alwaysAvailable = $step->metadata->alwaysAvailable;
                $contentMetadataUpdateStruct->mainLanguageCode = $step->metadata->mainLanguageCode;
                $contentMetadataUpdateStruct->mainLocationId = $step->metadata->mainLocationId;
                $contentMetadataUpdateStruct->modificationDate = $step->metadata->modificationDate;
                if ($step->metadata->name !== null) {
                    $contentMetadataUpdateStruct->name = $step->metadata->name;
                }
                $contentMetadataUpdateStruct->ownerId = $step->metadata->ownerId;
                $contentMetadataUpdateStruct->publishedDate = $step->metadata->publishedDate;

                $this->contentService->updateContentMetadata($content->contentInfo, $contentMetadataUpdateStruct);
            }

            $contentDraft = $this->contentService->updateContent($contentDraft->versionInfo, $contentUpdateStruct);
            $content = $this->contentService->publishVersion($contentDraft->versionInfo);

            $this->getLogger()->notice(sprintf(
                'Updated content "%s" (ID: %s) of type "%s"',
                $content->getName(),
                $content->id,
                $contentType->identifier,
            ));

            $updatedContentItems[] = $content;
        }

        return $updatedContentItems;
    }

    protected function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        Assert::isArray($executionResult);
        Assert::allIsInstanceOf($executionResult, Content::class);

        foreach ($executionResult as $valueObject) {
            foreach ($step->getActions() as $action) {
                $this->actionExecutor->handle($action, $valueObject);
            }
        }
    }
}

class_alias(ContentUpdateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ContentUpdateStepExecutor');
