<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\DocumentMapper;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Contracts\Core\Persistence\Content\Section;
use Ibexa\Contracts\Core\Persistence\Content\Type as ContentType;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Search\Document;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\BooleanField;
use Ibexa\Contracts\Core\Search\FieldType\DateField;
use Ibexa\Contracts\Core\Search\FieldType\FullTextField;
use Ibexa\Contracts\Core\Search\FieldType\IdentifierField;
use Ibexa\Contracts\Core\Search\FieldType\IntegerField;
use Ibexa\Contracts\Core\Search\FieldType\MultipleIdentifierField;
use Ibexa\Contracts\Core\Search\FieldType\MultipleStringField;
use Ibexa\Contracts\Core\Search\FieldType\StringField;
use Ibexa\Contracts\Elasticsearch\Mapping\ContentDocument;
use Ibexa\Contracts\Elasticsearch\Mapping\Event\ContentIndexCreateEvent;
use Ibexa\Contracts\Elasticsearch\Mapping\Event\LocationIndexCreateEvent;
use Ibexa\Contracts\Elasticsearch\Mapping\LocationDocument;
use Ibexa\Core\Persistence\FieldTypeRegistry;
use Ibexa\Core\Search\Common\FieldNameGenerator;
use Ibexa\Core\Search\Common\FieldRegistry;
use Ibexa\Elasticsearch\DocumentMapper\DocumentFactoryInterface as DocumentFactoryInterface;
use Ibexa\Elasticsearch\FieldType\SpellcheckField;
use Iterator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DocumentFactory implements DocumentFactoryInterface
{
    /** @var \Ibexa\Contracts\Core\Persistence\Handler */
    private $persistenceHandler;

    /** @var \Ibexa\Core\Search\Common\FieldRegistry */
    private $fieldRegistry;

    /** @var \Ibexa\Core\Persistence\FieldTypeRegistry */
    private $fieldTypeRegistry;

    /** @var \Ibexa\Core\Search\Common\FieldNameGenerator */
    private $fieldNameGenerator;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \Ibexa\Elasticsearch\DocumentMapper\DocumentIdGeneratorInterface */
    private $documentIdGenerator;

    public function __construct(
        PersistenceHandler $persistenceHandler,
        FieldRegistry $fieldRegistry,
        FieldNameGenerator $fieldNameGenerator,
        FieldTypeRegistry $fieldTypeRegistry,
        DocumentIdGeneratorInterface $documentIdGenerator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->fieldRegistry = $fieldRegistry;
        $this->fieldNameGenerator = $fieldNameGenerator;
        $this->fieldTypeRegistry = $fieldTypeRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->documentIdGenerator = $documentIdGenerator;
    }

    public function fromContent(Content $content): Iterator
    {
        $versionInfo = $content->versionInfo;
        $contentInfo = $content->versionInfo->contentInfo;

        $baseDocument = new ContentDocument();
        $baseDocument->fields[] = new Field(
            'content_id',
            $contentInfo->id,
            new IdentifierField()
        );

        $this->addContentInfoFields($baseDocument, $contentInfo);
        $this->addContentLocationFields($baseDocument, $content);

        foreach ($versionInfo->languageCodes as $languageCode) {
            $isMainTranslation = $contentInfo->mainLanguageCode === $languageCode;

            $document = clone $baseDocument;
            $document->id = $this->documentIdGenerator->generateContentDocumentId($contentInfo->id, $languageCode);
            $document->contentTypeId = $contentInfo->contentTypeId;
            $document->languageCode = $languageCode;
            $document->isMainTranslation = $isMainTranslation;
            $document->alwaysAvailable = $isMainTranslation && $contentInfo->alwaysAvailable;

            $this->addVersionInfoFields($document, $versionInfo);
            $this->addContentTranslationMetaFields($document, $contentInfo, $languageCode);
            $this->addContentTranslationDataFields($document, $content, $languageCode);

            $this->eventDispatcher->dispatch(new ContentIndexCreateEvent($content, $document));

            yield $document;
        }
    }

    public function fromLocation(Location $location, Content $content = null): Iterator
    {
        if ($content === null) {
            $content = $this->persistenceHandler->contentHandler()->load($location->contentId);
        }

        $versionInfo = $content->versionInfo;
        $contentInfo = $content->versionInfo->contentInfo;

        $baseDocument = new LocationDocument();
        $baseDocument->contentTypeId = $contentInfo->contentTypeId;

        $baseDocument->fields[] = new Field(
            'location_id',
            $location->id,
            new IdentifierField()
        );

        $baseDocument->fields[] = new Field(
            'content_id',
            $location->contentId,
            new IdentifierField()
        );

        $baseDocument->fields[] = new Field(
            'parent_id',
            $location->parentId,
            new IdentifierField()
        );

        $baseDocument->fields[] = new Field(
            'location_remote_id',
            $location->remoteId,
            new IdentifierField()
        );

        $baseDocument->fields[] = new Field(
            'path_string',
            $location->pathString,
            new IdentifierField()
        );

        $baseDocument->fields[] = new Field(
            'priority',
            $location->priority,
            new IntegerField(),
        );

        $baseDocument->fields[] = new Field(
            'depth',
            $location->depth,
            new IntegerField()
        );

        $baseDocument->fields[] = new Field(
            'location_ancestors',
            $this->getLocationAncestors($location),
            new MultipleIdentifierField()
        );

        $baseDocument->fields[] = new Field(
            'is_main_location',
            $location->id == $contentInfo->mainLocationId,
            new BooleanField(),
        );

        $baseDocument->fields[] = new Field(
            'hidden',
            $location->hidden,
            new BooleanField()
        );

        $baseDocument->fields[] = new Field(
            'invisible',
            $location->invisible,
            new BooleanField()
        );

        $this->addContentInfoFields($baseDocument, $contentInfo);

        foreach ($versionInfo->languageCodes as $languageCode) {
            $isMainTranslation = $contentInfo->mainLanguageCode === $languageCode;

            $document = clone $baseDocument;
            $document->id = $this->documentIdGenerator->generateLocationDocumentId($location->id, $languageCode);
            $document->contentTypeId = $contentInfo->contentTypeId;
            $document->languageCode = $languageCode;
            $document->isMainTranslation = $isMainTranslation;
            $document->alwaysAvailable = $isMainTranslation && $contentInfo->alwaysAvailable;

            $this->addVersionInfoFields($document, $versionInfo);
            $this->addContentTranslationMetaFields($document, $contentInfo, $languageCode);
            $this->addContentTranslationDataFields($document, $content, $languageCode);

            $this->eventDispatcher->dispatch(new LocationIndexCreateEvent($location, $document));

            yield $document;
        }
    }

    private function addContentLocationFields(Document $document, Content $content): void
    {
        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $content->versionInfo->contentInfo->id
        );

        $mainLocation = null;
        $isSomeLocationVisible = false;
        $locationData = [];

        foreach ($locations as $location) {
            $locationData['ids'][] = $location->id;
            $locationData['parent_ids'][] = $location->parentId;
            $locationData['remote_ids'][] = $location->remoteId;
            $locationData['path_strings'][] = $location->pathString;

            $ancestorsIds = $this->getLocationAncestors($location);
            foreach ($ancestorsIds as $ancestorId) {
                if (!in_array($ancestorId, $locationData['ancestors'] ?? [])) {
                    $locationData['ancestors'][] = $ancestorId;
                }
            }

            if ($location->id == $content->versionInfo->contentInfo->mainLocationId) {
                $mainLocation = $location;
            }

            if (!$location->hidden && !$location->invisible) {
                $isSomeLocationVisible = true;
            }
        }

        if (!empty($locationData)) {
            $document->fields[] = new Field(
                'location_id',
                $locationData['ids'],
                new MultipleIdentifierField()
            );

            $document->fields[] = new Field(
                'location_parent_id',
                $locationData['parent_ids'],
                new MultipleIdentifierField()
            );

            $document->fields[] = new Field(
                'location_remote_id',
                $locationData['remote_ids'],
                new MultipleIdentifierField()
            );

            $document->fields[] = new Field(
                'location_path_string',
                $locationData['path_strings'],
                new MultipleIdentifierField()
            );

            $document->fields[] = new Field(
                'location_ancestors',
                $locationData['ancestors'],
                new MultipleIdentifierField()
            );
        }

        if ($mainLocation !== null) {
            $document->fields[] = new Field(
                'main_location',
                $mainLocation->id,
                new IdentifierField()
            );

            $document->fields[] = new Field(
                'main_location_parent',
                $mainLocation->parentId,
                new IdentifierField()
            );

            $document->fields[] = new Field(
                'main_location_remote_id',
                $mainLocation->remoteId,
                new IdentifierField()
            );

            $document->fields[] = new Field(
                'main_location_visible',
                !$mainLocation->hidden && !$mainLocation->invisible,
                new BooleanField()
            );

            $document->fields[] = new Field(
                'main_location_path',
                $mainLocation->pathString,
                new IdentifierField()
            );

            $document->fields[] = new Field(
                'main_location_depth',
                $mainLocation->depth,
                new IntegerField()
            );

            $document->fields[] = new Field(
                'main_location_priority',
                $mainLocation->priority,
                new IntegerField()
            );
        }

        $document->fields[] = new Field(
            'location_visible',
            $isSomeLocationVisible,
            new BooleanField()
        );
    }

    private function addContentTranslationDataFields(Document $document, Content $content, string $languageCode): void
    {
        $contentType = $this->persistenceHandler->contentTypeHandler()->load(
            $content->versionInfo->contentInfo->contentTypeId
        );

        $text = [];
        foreach ($content->fields as $field) {
            if ($field->languageCode !== $languageCode) {
                continue;
            }

            foreach ($contentType->fieldDefinitions as $fieldDefinition) {
                if ($fieldDefinition->id !== $field->fieldDefinitionId || !$fieldDefinition->isSearchable) {
                    continue;
                }

                /** @var \Ibexa\Core\Persistence\FieldType $fieldType */
                $fieldType = $this->fieldTypeRegistry->getFieldType($fieldDefinition->fieldType);

                $document->fields[] = new Field(
                    $this->fieldNameGenerator->getName('is_empty', $fieldDefinition->identifier),
                    $fieldType->isEmptyValue($field->value),
                    new BooleanField()
                );

                $indexFields = $this->fieldRegistry
                    ->getType($field->type)
                    ->getIndexData($field, $fieldDefinition);

                foreach ($indexFields as $indexField) {
                    if ($indexField->getValue() === null) {
                        continue;
                    }

                    $document->fields[] = new Field(
                        $this->fieldNameGenerator->getName(
                            $indexField->getName(),
                            $fieldDefinition->identifier,
                            $contentType->identifier
                        ),
                        $indexField->getValue(),
                        $indexField->getType()
                    );

                    if ($indexField->getType() instanceof FullTextField) {
                        $value = $indexField->getValue();
                        if (empty($value)) {
                            continue;
                        }

                        if (is_array($value)) {
                            $text = array_merge($text, $value);

                            continue;
                        }

                        $text[] = $value;
                    }
                }
            }
        }

        $document->fields[] = new Field(
            'spellcheck_content_text',
            implode(' ', $text),
            new SpellcheckField()
        );
    }

    private function addContentTranslationMetaFields(Document $document, ContentInfo $contentInfo, string $languageCode): void
    {
        $isMainTranslation = $languageCode === $contentInfo->mainLanguageCode;

        $document->fields[] = new Field(
            'meta_indexed_language_code',
            $languageCode,
            new StringField()
        );

        $document->fields[] = new Field(
            'meta_indexed_is_main_translation',
            $isMainTranslation,
            new BooleanField()
        );

        $document->fields[] = new Field(
            'meta_indexed_is_main_translation_and_always_available',
            $isMainTranslation && $contentInfo->alwaysAvailable,
            new BooleanField()
        );
    }

    private function addContentInfoFields(Document $document, ContentInfo $contentInfo): void
    {
        $section = $this->persistenceHandler->sectionHandler()->load($contentInfo->sectionId);
        $contentType = $this->persistenceHandler->contentTypeHandler()->load($contentInfo->contentTypeId);

        $document->fields[] = new Field(
            'content_remote_id',
            $contentInfo->remoteId,
            new IdentifierField()
        );

        $document->fields[] = new Field(
            'content_name',
            $contentInfo->name,
            new StringField()
        );

        $this->addContentTypeFields($document, $contentType);
        $this->addUserMetadataFields($document, $contentInfo);

        $this->addLanguagesFields($document, $contentInfo);
        $this->addSectionFields($document, $section);
        $this->addDateMetadataFields($document, $contentInfo);
        $this->addObjectStateFields($document, $contentInfo);
    }

    private function addLanguagesFields(Document $document, ContentInfo $contentInfo): void
    {
        $document->fields[] = new Field(
            'content_main_language_code',
            $contentInfo->mainLanguageCode,
            new IdentifierField(),
        );

        $document->fields[] = new Field(
            'content_always_available',
            $contentInfo->alwaysAvailable,
            new BooleanField(),
        );
    }

    private function addContentTypeFields(Document $document, ContentType $contentType): void
    {
        $document->fields[] = new Field(
            'content_type_id',
            $contentType->id,
            new IdentifierField()
        );

        $document->fields[] = new Field(
            'content_type_group_id',
            $contentType->groupIds,
            new MultipleIdentifierField()
        );

        $document->fields[] = new Field(
            'content_type_is_container',
            $contentType->isContainer,
            new BooleanField()
        );
    }

    private function addObjectStateFields(Document $document, ContentInfo $contentInfo): void
    {
        $document->fields[] = new Field(
            'object_state_id',
            $this->getObjectStateIds($contentInfo->id),
            new MultipleIdentifierField()
        );
    }

    private function addDateMetadataFields(Document $document, ContentInfo $contentInfo): void
    {
        $document->fields[] = new Field(
            'content_modification_date',
            $contentInfo->modificationDate,
            new DateField()
        );

        $document->fields[] = new Field(
            'content_publication_date',
            $contentInfo->publicationDate,
            new DateField()
        );
    }

    private function addUserMetadataFields(Document $document, ContentInfo $contentInfo): void
    {
        $document->fields[] = new Field(
            'content_owner_user_id',
            $contentInfo->ownerId,
            new IdentifierField()
        );

        $document->fields[] = new Field(
            'content_owner_user_group_id',
            $this->getContentOwnerUserGroupIds($contentInfo),
            new MultipleIdentifierField()
        );
    }

    private function addSectionFields(Document $document, Section $section): void
    {
        $document->fields[] = new Field(
            'section_id',
            $section->id,
            new IdentifierField()
        );

        $document->fields[] = new Field(
            'section_identifier',
            $section->identifier,
            new IdentifierField(['raw' => true])
        );

        $document->fields[] = new Field(
            'section_name',
            $section->name,
            new StringField()
        );
    }

    private function addVersionInfoFields(Document $document, VersionInfo $versionInfo): void
    {
        $document->fields[] = new Field(
            'content_translated_name',
            $versionInfo->names[$document->languageCode] ?? '',
            new StringField()
        );

        $document->fields[] = new Field(
            'content_language_codes',
            $versionInfo->languageCodes,
            new MultipleStringField()
        );

        $document->fields[] = new Field(
            'content_version_no',
            $versionInfo->versionNo,
            new IntegerField()
        );

        $document->fields[] = new Field(
            'content_language_codes_raw',
            $versionInfo->languageCodes,
            new MultipleIdentifierField(['raw' => true])
        );

        $document->fields[] = new Field(
            'content_version_creator_user_id',
            $versionInfo->creatorId,
            new IdentifierField()
        );
    }

    private function getObjectStateIds(int $contentId): array
    {
        $objectStateIds = [];

        $objectStateHandler = $this->persistenceHandler->objectStateHandler();
        foreach ($objectStateHandler->loadAllGroups() as $objectStateGroup) {
            try {
                $objectStateIds[] = $objectStateHandler->getContentState(
                    $contentId,
                    $objectStateGroup->id
                )->id;
            } catch (NotFoundException $e) {
                // Ignore empty object state groups
            }
        }

        return $objectStateIds;
    }

    /**
     * @return int[]
     */
    private function getContentOwnerUserGroupIds(ContentInfo $contentInfo): array
    {
        $locationHandler = $this->persistenceHandler->locationHandler();

        $locationIds = [];
        foreach ($locationHandler->loadLocationsByContent($contentInfo->ownerId) as $location) {
            $path = explode('/', trim($location->pathString, '/'));
            // Remove Location of Content with $contentId
            array_pop($path);
            // Remove Root Location id
            array_shift($path);

            $locationIds = array_merge($locationIds, $path);
        }

        $contentIds = [];
        if (!empty($locationIds)) {
            $locationIds = array_unique($locationIds);
            foreach ($locationHandler->loadList($locationIds) as $location) {
                $contentIds[] = $location->contentId;
            }
        }

        // Add owner user id as it can also be considered as user group.
        $contentIds[] = $contentInfo->ownerId;

        return array_unique($contentIds);
    }

    private function getLocationAncestors(Location $location): array
    {
        $ancestorsIds = explode('/', trim($location->pathString, '/'));
        // Remove $location->id from ancestors
        array_pop($ancestorsIds);

        return $ancestorsIds;
    }
}

class_alias(DocumentFactory::class, 'Ibexa\Platform\ElasticSearchEngine\DocumentMapper\DocumentFactory');
