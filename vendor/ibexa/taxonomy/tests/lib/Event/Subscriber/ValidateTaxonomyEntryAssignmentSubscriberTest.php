<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeUpdateContentEvent;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use Ibexa\Taxonomy\Event\Subscriber\ValidateTaxonomyEntryAssignmentSubscriber;
use Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Taxonomy\Event\Subscriber\ValidateTaxonomyEntryAssignmentSubscriber
 */
final class ValidateTaxonomyEntryAssignmentSubscriberTest extends TestCase
{
    public const IBEXA_ENTRY_ASSIGNMENT_TYPE_IDENTIFIER = 'ibexa_entry_assignment';

    /** @var \Ibexa\Contracts\Core\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject */
    private ContentService $contentService;

    /** @var \Ibexa\Core\FieldType\FieldType|\PHPUnit\Framework\MockObject\MockObject */
    private FieldType $taxonomyEntryAssignmentFieldType;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver|\PHPUnit\Framework\MockObject\MockObject */
    private PermissionResolver $permissionResolver;

    protected function setUp(): void
    {
        $this->contentService = $this->createMock(ContentService::class);
        $this->taxonomyEntryAssignmentFieldType = $this->createMock(FieldType::class);
        $this->permissionResolver = $this->createMock(PermissionResolver::class);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testOnBeforeUpdateContent(): void
    {
        $stringField = $this->getTextLineField();
        $entryAssignmentField = $this->getEntryAssignmentField();
        $taxonomyEntry = $this->getTaxonomyEntry();
        $updatedEntryAssignmentField = $this->getUpdatedEntryAssignmentField($taxonomyEntry);
        $versionInfo = $this->getVersionInfo();
        $contentUpdateStruct = new ContentUpdateStruct([
            'fields' => [$stringField, $updatedEntryAssignmentField],
        ]);
        $fieldDefinitions = $this->getFieldDefinitions();
        $fieldDefinitionCollection = new FieldDefinitionCollection($fieldDefinitions);
        $contentType = new ContentType([
            'fieldDefinitions' => $fieldDefinitionCollection,
        ]);
        $content = new Content([
            'versionInfo' => $versionInfo,
            'contentType' => $contentType,
            'internalFields' => [$stringField, $entryAssignmentField],
        ]);

        $this->taxonomyEntryAssignmentFieldType
            ->method('getFieldTypeIdentifier')
            ->willReturn(self::IBEXA_ENTRY_ASSIGNMENT_TYPE_IDENTIFIER);

        $this->contentService
            ->method('loadContentByVersionInfo')
            ->with($versionInfo)
            ->willReturn($content);

        $this->permissionResolver
            ->method('hasAccess')
            ->with('taxonomy', 'assign')
            ->willReturn(true);

        $subscriber = new ValidateTaxonomyEntryAssignmentSubscriber(
            $this->contentService,
            $this->taxonomyEntryAssignmentFieldType,
            $this->permissionResolver,
        );
        $event = new BeforeUpdateContentEvent($versionInfo, $contentUpdateStruct);

        $subscriber->onBeforeUpdateContent($event);
    }

    public function testOnBeforeUpdateContentThrowsOnUnauthorizedAccess(): void
    {
        $stringField = $this->getTextLineField();
        $entryAssignmentField = $this->getEntryAssignmentField();
        $taxonomyEntry = $this->getTaxonomyEntry();
        $updatedEntryAssignmentField = $this->getUpdatedEntryAssignmentField($taxonomyEntry);
        $fields = [$stringField, $updatedEntryAssignmentField];
        $versionInfo = new VersionInfo();
        $contentUpdateStruct = new ContentUpdateStruct(['fields' => $fields]);
        $fieldDefinitions = $this->getFieldDefinitions();
        $fieldDefinitionCollection = new FieldDefinitionCollection($fieldDefinitions);
        $contentType = new ContentType(['fieldDefinitions' => $fieldDefinitionCollection]);
        $content = new Content([
            'versionInfo' => $this->getVersionInfo(),
            'contentType' => $contentType,
            'internalFields' => [$stringField, $entryAssignmentField],
        ]);

        $this->taxonomyEntryAssignmentFieldType
            ->method('getFieldTypeIdentifier')
            ->willReturn(self::IBEXA_ENTRY_ASSIGNMENT_TYPE_IDENTIFIER);

        $this->contentService
            ->method('loadContentByVersionInfo')
            ->with($versionInfo)
            ->willReturn($content);

        $this->permissionResolver
            ->method('hasAccess')
            ->with('taxonomy', 'assign')
            ->willReturn(false);

        $subscriber = new ValidateTaxonomyEntryAssignmentSubscriber(
            $this->contentService,
            $this->taxonomyEntryAssignmentFieldType,
            $this->permissionResolver,
        );
        $event = new BeforeUpdateContentEvent($versionInfo, $contentUpdateStruct);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage("The User does not have the 'read' 'taxonomy' permission with: contentId '1'");

        $subscriber->onBeforeUpdateContent($event);
    }

    /**
     * @return array<\Ibexa\Core\Repository\Values\ContentType\FieldDefinition>
     */
    private function getFieldDefinitions(): array
    {
        return [
            new FieldDefinition([
                'identifier' => 'foo',
                'fieldTypeIdentifier' => 'ibexa_string',
            ]),
            new FieldDefinition([
                'identifier' => 'bar',
                'fieldTypeIdentifier' => self::IBEXA_ENTRY_ASSIGNMENT_TYPE_IDENTIFIER,
            ]),
        ];
    }

    private function getTaxonomyEntry(): TaxonomyEntry
    {
        return new TaxonomyEntry(
            1,
            'test',
            'Test',
            'eng-GB',
            [
                'eng-GB' => 'Test',
            ],
            null,
            new Content(),
            'tags',
        );
    }

    private function getTextLineField(): Field
    {
        return new Field(
            [
                'fieldDefIdentifier' => 'foo',
                'fieldTypeIdentifier' => 'ibexa_string',
                'value' => new TextLineValue(),
                'languageCode' => 'eng-GB',
            ]
        );
    }

    private function getEntryAssignmentField(): Field
    {
        return new Field(
            [
                'fieldDefIdentifier' => 'bar',
                'fieldTypeIdentifier' => self::IBEXA_ENTRY_ASSIGNMENT_TYPE_IDENTIFIER,
                'value' => new Value([], 'tags'),
                'languageCode' => 'eng-GB',
            ]
        );
    }

    private function getVersionInfo(): VersionInfo
    {
        return new VersionInfo([
            'contentInfo' => new ContentInfo(['id' => 1]),
        ]);
    }

    private function getUpdatedEntryAssignmentField(TaxonomyEntry $taxonomyEntry): Field
    {
        return new Field(
            [
                'fieldDefIdentifier' => 'bar',
                'fieldTypeIdentifier' => self::IBEXA_ENTRY_ASSIGNMENT_TYPE_IDENTIFIER,
                'value' => new Value([$taxonomyEntry], 'tags'),
                'languageCode' => 'eng-GB',
            ]
        );
    }
}
