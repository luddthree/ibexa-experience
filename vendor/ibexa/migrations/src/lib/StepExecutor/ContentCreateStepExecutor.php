<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Content\Location;
use Ibexa\Migration\ValueObject\Content\Metadata\Section;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\ContentCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

final class ContentCreateStepExecutor extends AbstractStepExecutor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Migration\Service\FieldTypeServiceInterface */
    private $fieldTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface */
    private $actionExecutor;

    /** @var \Ibexa\Contracts\Core\Repository\SectionService */
    private $sectionService;

    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        FieldTypeServiceInterface $fieldTypeService,
        LocationService $locationService,
        SectionService $sectionService,
        ExecutorInterface $actionExecutor,
        ?LoggerInterface $logger = null
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->actionExecutor = $actionExecutor;
        $this->fieldTypeService = $fieldTypeService;
        $this->sectionService = $sectionService;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ContentCreateStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ContentCreateStep $step
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function doHandle(StepInterface $step): ValueObject
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($step->metadata->contentType);

        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $step->metadata->mainTranslation);
        $contentCreateStruct->remoteId = $step->metadata->remoteId;
        $contentCreateStruct->ownerId = $step->metadata->creatorId;
        $contentCreateStruct->alwaysAvailable = $step->metadata->alwaysAvailable;
        $contentCreateStruct->modificationDate = $step->metadata->modificationDate;
        $contentCreateStruct->sectionId = $this->getSectionId($step->metadata->section);

        $locationParentId = $this->getLocationParentId($step->location);

        $locationCreateStruct = $this->locationService->newLocationCreateStruct($locationParentId);
        $locationCreateStruct->remoteId = $step->location->remoteId;
        if ($step->location->priority !== null) {
            $locationCreateStruct->priority = $step->location->priority;
        }
        if ($step->location->hidden !== null) {
            $locationCreateStruct->hidden = $step->location->hidden;
        }
        $locationCreateStruct->sortField = $step->location->sortField;
        $locationCreateStruct->sortOrder = $step->location->sortOrder;

        foreach ($step->fields as $field) {
            $fieldDefinition = $contentType->getFieldDefinition($field->fieldDefIdentifier);
            Assert::notNull($fieldDefinition, sprintf(
                'Field definition with identifier: "%s" does not exist in content type "%s".',
                $field->fieldDefIdentifier,
                $contentType->identifier,
            ));

            $value = $this->fieldTypeService->getFieldValueFromHash(
                $field->value,
                $fieldDefinition->fieldTypeIdentifier,
                $fieldDefinition->getFieldSettings()
            );

            $contentCreateStruct->setField($field->fieldDefIdentifier, $value, $field->languageCode);
        }

        $content = $this->contentService->createContent(
            $contentCreateStruct,
            [$locationCreateStruct],
            [/* Disable validation as content is immediately published and validation will occur there */]
        );
        $this->contentService->publishVersion($content->getVersionInfo());
        $content = $this->contentService->loadContent($content->id);

        $this->getLogger()->notice(sprintf(
            'Added content "%s" (ID: %s) of type "%s"',
            $content->getName(),
            $content->id,
            $content->getContentType()->identifier,
        ));

        return $content;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getLocationParentId(Location $location): int
    {
        if (empty($location->parentLocationId)) {
            assert(!empty($location->parentLocationRemoteId));
            $parentLocation = $this->locationService->loadLocationByRemoteId($location->parentLocationRemoteId);

            return $parentLocation->id;
        }

        return (int) $location->parentLocationId;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        Assert::isInstanceOf($executionResult, Content::class);

        foreach ($step->getActions() as $action) {
            $this->actionExecutor->handle($action, $executionResult);
        }
    }

    private function getSectionId(?Section $section): ?int
    {
        if ($section === null) {
            return null;
        }

        if ($section->getId() !== null) {
            return $section->getId();
        }

        if ($section->getIdentifier() !== null) {
            return $this->sectionService->loadSectionByIdentifier($section->getIdentifier())->id;
        }

        return null;
    }
}

class_alias(ContentCreateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ContentCreateStepExecutor');
