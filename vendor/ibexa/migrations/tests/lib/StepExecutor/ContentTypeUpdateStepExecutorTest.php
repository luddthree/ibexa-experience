<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\OutOfBoundsException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition as ApiFieldDefinition;
use Ibexa\Migration\StepExecutor\ContentTypeUpdateStepExecutor;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinition;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\ValueObject\ContentType\Matcher;
use Ibexa\Migration\ValueObject\ContentType\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\Action\ContentType\AssignContentTypeGroup;
use Ibexa\Migration\ValueObject\Step\Action\ContentType\RemoveDrafts;
use Ibexa\Migration\ValueObject\Step\Action\ContentType\RemoveFieldByIdentifier;
use Ibexa\Migration\ValueObject\Step\Action\ContentType\UnassignContentTypeGroup;
use Ibexa\Migration\ValueObject\Step\ContentTypeUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;

/**
 * @covers \Ibexa\Migration\StepExecutor\ContentTypeUpdateStepExecutor
 */
final class ContentTypeUpdateStepExecutorTest extends AbstractContentTypeExecutorTest
{
    private const DEFAULT_SORT_FIELD = 1;
    private const DEFAULT_SORT_ORDER = 2;
    private const FIELD_POSITION = 2;
    private const MAX_STRING_LENGTH = 128;

    /** @var \Ibexa\Migration\StepExecutor\ContentTypeUpdateStepExecutor */
    private $executor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->executor = new ContentTypeUpdateStepExecutor(
            self::getUserService(),
            self::getContentTypeService(),
            self::getFieldTypeService(),
            self::getContentTypeActionExecutor(),
            self::getContentTypeFinderRegistry(),
            'admin',
        );

        $this->configureExecutor($this->executor, [
            ResolverInterface::class => self::getReferenceResolver('content_type'),
        ]);
    }

    /**
     * @return iterable<array{\Ibexa\Migration\ValueObject\Step\ContentTypeUpdateStep}>
     */
    public function provideStepsForDefaultHandling(): iterable
    {
        yield [
            $this->createStep(new Matcher('content_type_identifier', 'article')),
        ];

        yield [
            $this->createStep(new Matcher('location_remote_id', 'c15b600eb9198b1924063b5a68758232')),
        ];
    }

    public function testCanHandleStepWithoutTranslations(): void
    {
        $contentType = $this->findContentType();
        self::assertNotNull($contentType);
        $titleField = $contentType->fieldDefinitions->get('title');
        self::assertEquals('Title', $titleField->getName());

        $step = $this->createStepWithoutTranslations();
        $this->executor->handle($step);

        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $this->findContentType();

        self::assertEquals('2020-12-11T08:26:43+00:00', $contentType->modificationDate->format('c'));
        self::assertEquals('__REMOTE_ID__', $contentType->remoteId);
        self::assertEquals('__URL_ALIAS_SCHEMA__', $contentType->urlAliasSchema);
        self::assertEquals('__NAME_SCHEMA__', $contentType->nameSchema);
        self::assertTrue($contentType->defaultAlwaysAvailable);
        self::assertEquals(self::DEFAULT_SORT_FIELD, $contentType->defaultSortField);
        self::assertEquals(self::DEFAULT_SORT_ORDER, $contentType->defaultSortOrder);

        $titleField = $contentType->fieldDefinitions->get('title');
        self::assertInstanceOf(ApiFieldDefinition::class, $titleField);
        self::assertEquals(self::FIELD_POSITION, $titleField->position);
        self::assertTrue($titleField->isRequired);
        self::assertFalse($titleField->isSearchable);
        self::assertTrue($titleField->isInfoCollector);
        self::assertFalse($titleField->isTranslatable);
        self::assertFalse($titleField->isThumbnail);
        self::assertEquals('Title', $titleField->getName());
        self::assertEquals('__NEW_DEFAULT_NAME__', $titleField->defaultValue);
        self::assertEquals(self::MAX_STRING_LENGTH, $titleField->validatorConfiguration['StringLengthValidator']['maxStringLength']);
    }

    /**
     * @dataProvider provideStepsForDefaultHandling
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testDefaultHandling(ContentTypeUpdateStep $step): void
    {
        $contentType = $this->findContentType();
        self::assertNotNull($contentType);

        // Create a draft belonging to someone else
        self::setAnonymousUser();
        self::getServiceByClassName(Repository::class)->sudo(static function () use ($contentType): void {
            self::getContentTypeService()->createContentTypeDraft($contentType);
        });
        self::setAdministratorUser();

        $titleField = $contentType->fieldDefinitions->get('title');
        self::assertEquals('Title', $titleField->getName());
        self::assertNotSame('Blog', $contentType->getName());
        self::assertNotSame('__DESCRIPTION__', $contentType->getDescription());

        $beforeUpdateModificationDate = $contentType->modificationDate;

        $this->executor->handle($step);

        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $this->findContentType();

        self::assertGreaterThan($beforeUpdateModificationDate, $contentType->modificationDate);
        self::assertSame('__REMOTE_ID__', $contentType->remoteId);
        self::assertSame('__URL_ALIAS_SCHEMA__', $contentType->urlAliasSchema);
        self::assertSame('__NAME_SCHEMA__', $contentType->nameSchema);
        self::assertTrue($contentType->defaultAlwaysAvailable);
        self::assertSame(self::DEFAULT_SORT_FIELD, $contentType->defaultSortField);
        self::assertSame(self::DEFAULT_SORT_ORDER, $contentType->defaultSortOrder);
        self::assertSame('Blog', $contentType->getName());
        self::assertSame('__DESCRIPTION__', $contentType->getDescription());

        $titleField = $contentType->fieldDefinitions->get('title');
        self::assertInstanceOf(ApiFieldDefinition::class, $titleField);
        self::assertEquals(self::FIELD_POSITION, $titleField->position);
        self::assertTrue($titleField->isRequired);
        self::assertFalse($titleField->isSearchable);
        self::assertTrue($titleField->isInfoCollector);
        self::assertFalse($titleField->isTranslatable);
        self::assertFalse($titleField->isThumbnail);
        self::assertEquals('Name', $titleField->getName());
        self::assertEquals('__NEW_DEFAULT_NAME__', $titleField->defaultValue);
        self::assertEquals(self::MAX_STRING_LENGTH, $titleField->validatorConfiguration['StringLengthValidator']['maxStringLength']);

        //Assertions for executed Actions
        $assignedTypeGroupIdentifiers = array_map(static function (ContentTypeGroup $group) {
            return $group->identifier;
        }, $contentType->contentTypeGroups);
        self::assertEquals(['Media'], $assignedTypeGroupIdentifiers);

        self::assertFalse($contentType->fieldDefinitions->has('name'));
        self::assertFalse($this->hasDraft($contentType));
    }

    public function testHandlingStepWithoutRequiredAndPositionDefinedInField(): void
    {
        $contentType = $this->findContentType();
        self::assertNotNull($contentType);
        $titleField = $contentType->fieldDefinitions->get('title');
        self::assertEquals('Title', $titleField->getName());
        self::assertEquals(1, $titleField->position);
        self::assertTrue($titleField->isRequired);

        $step = $this->createStepWithoutRequiredAndPositionDefinedInField();
        $this->executor->handle($step);

        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $this->findContentType();

        $titleField = $contentType->fieldDefinitions->get('title');
        self::assertInstanceOf(ApiFieldDefinition::class, $titleField);
        self::assertEquals(1, $titleField->position);
        self::assertTrue($titleField->isRequired);
    }

    public function testHandlingStepWithNewField(): void
    {
        $contentType = $this->findContentType();
        self::assertNotNull($contentType);

        $found = true;
        try {
            $contentType->fieldDefinitions->get('__NEW_FIELD__');
        } catch (OutOfBoundsException $e) {
            $found = false;
        }
        self::assertFalse($found);

        $step = $this->createStepNewField();
        $this->executor->handle($step);

        $contentType = $this->findContentType();
        self::assertInstanceOf(ContentType::class, $contentType);

        $newField = $contentType->fieldDefinitions->get('__NEW_FIELD__');
        self::assertInstanceOf(ApiFieldDefinition::class, $newField);
        self::assertEquals(self::FIELD_POSITION, $newField->position);
        self::assertTrue($newField->isRequired);
        self::assertFalse($newField->isSearchable);
        self::assertTrue($newField->isInfoCollector);
        self::assertFalse($newField->isTranslatable);
        self::assertFalse($newField->isThumbnail);
        self::assertEquals('__NEW_DEFAULT_FIELD_NAME__', $newField->defaultValue);
        self::assertEquals(self::MAX_STRING_LENGTH, $newField->validatorConfiguration['StringLengthValidator']['maxStringLength']);
    }

    public function testHandlingStepWithUpdateFieldDefinitionType(): void
    {
        $contentType = $this->findContentType();
        self::assertNotNull($contentType);

        $fieldTitle = $contentType->fieldDefinitions->get('title');
        self::assertEquals('ezstring', $fieldTitle->fieldTypeIdentifier);

        $step = $this->createStepWithChangedFieldType();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Field definition type cannot be updated. In order to change field definition type remove field with old type and add a new one.');
        $this->executor->handle($step);
    }

    private function findContentType(): ?ContentType
    {
        try {
            return self::getContentTypeService()->loadContentTypeByIdentifier('article');
        } catch (NotFoundException $e) {
            return null;
        }
    }

    private function hasDraft(ContentType $contentType): bool
    {
        try {
            self::getContentTypeService()->loadContentTypeDraft($contentType->id);

            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }

    private function createStep(Matcher $matcher): ContentTypeUpdateStep
    {
        $metadata = UpdateMetadata::createFromArray([
            'identifier' => 'article',
            'mainTranslation' => 'eng-GB',
            'modifierId' => 14,
            'modificationDate' => '2020-12-11T08:26:43+00:00',
            'remoteId' => '__REMOTE_ID__',
            'urlAliasSchema' => '__URL_ALIAS_SCHEMA__',
            'nameSchema' => '__NAME_SCHEMA__',
            'container' => false,
            'defaultAlwaysAvailable' => true,
            'defaultSortField' => self::DEFAULT_SORT_FIELD,
            'defaultSortOrder' => self::DEFAULT_SORT_ORDER,
            'translations' => [
                'eng-GB' => [
                    'name' => 'Blog',
                    'description' => '__DESCRIPTION__',
                ],
            ],
        ]);

        $fieldDefinitionCollection = FieldDefinitionCollection::create([
            FieldDefinition::fromAPIFieldDefinition(
                new ApiFieldDefinition([
                    'identifier' => 'title',
                    'fieldTypeIdentifier' => 'ezstring',
                    'position' => self::FIELD_POSITION,
                    'fieldGroup' => '__FIELD_GROUP__',
                    'isRequired' => true,
                    'isSearchable' => false,
                    'isTranslatable' => false,
                    'isThumbnail' => false,
                    'isInfoCollector' => true,
                    'validatorConfiguration' => [
                        'StringLengthValidator' => [
                            'maxStringLength' => self::MAX_STRING_LENGTH,
                            'minStringLength' => null,
                        ],
                    ],
                    'names' => [
                        'eng-GB' => 'Name',
                    ],
                    'descriptions' => [
                        'eng-GB' => 'Name or title of the blog.',
                    ],
                ]),
                '__NEW_DEFAULT_NAME__'
            ),
            FieldDefinition::fromAPIFieldDefinition(
                new ApiFieldDefinition([
                    'identifier' => 'name',
                    'fieldTypeIdentifier' => 'ezstring',
                    'fieldGroup' => '__FIELD_GROUP__',
                    'isRequired' => true,
                    'isSearchable' => false,
                    'isTranslatable' => false,
                    'isThumbnail' => false,
                    'isInfoCollector' => true,
                    'names' => [
                        'eng-GB' => 'Name',
                    ],
                    'descriptions' => [
                        'eng-GB' => 'Description',
                    ],
                ]),
                '__NEW_DEFAULT_NAME_2__'
            ),
        ]);

        return new ContentTypeUpdateStep(
            $metadata,
            $fieldDefinitionCollection,
            $matcher,
            [
                new AssignContentTypeGroup('Media'),
                new UnassignContentTypeGroup('Content'),
                new RemoveFieldByIdentifier('name'),
                new RemoveDrafts(),
            ]
        );
    }

    private function createStepWithoutTranslations(): StepInterface
    {
        $metadata = UpdateMetadata::createFromArray([
            'identifier' => 'article',
            'mainTranslation' => 'eng-GB',
            'modifierId' => 14,
            'modificationDate' => '2020-12-11T08:26:43+00:00',
            'remoteId' => '__REMOTE_ID__',
            'urlAliasSchema' => '__URL_ALIAS_SCHEMA__',
            'nameSchema' => '__NAME_SCHEMA__',
            'container' => false,
            'defaultAlwaysAvailable' => true,
            'defaultSortField' => self::DEFAULT_SORT_FIELD,
            'defaultSortOrder' => self::DEFAULT_SORT_ORDER,
            'translations' => [],
        ]);

        $fieldDefinitionCollection = FieldDefinitionCollection::create([
            FieldDefinition::fromAPIFieldDefinition(
                new ApiFieldDefinition([
                    'identifier' => 'title',
                    'fieldTypeIdentifier' => 'ezstring',
                    'position' => self::FIELD_POSITION,
                    'fieldGroup' => '__FIELD_GROUP__',
                    'isRequired' => true,
                    'isSearchable' => false,
                    'isTranslatable' => false,
                    'isThumbnail' => false,
                    'isInfoCollector' => true,
                    'validatorConfiguration' => [
                        'StringLengthValidator' => [
                            'maxStringLength' => self::MAX_STRING_LENGTH,
                            'minStringLength' => null,
                        ],
                    ],
                ]),
                '__NEW_DEFAULT_NAME__'
            ),
            FieldDefinition::fromAPIFieldDefinition(
                new ApiFieldDefinition([
                    'identifier' => 'name',
                    'fieldTypeIdentifier' => 'ezstring',
                    'fieldGroup' => '__FIELD_GROUP__',
                    'isRequired' => true,
                    'isSearchable' => false,
                    'isTranslatable' => false,
                    'isThumbnail' => false,
                    'isInfoCollector' => true,
                ]),
                '__NEW_DEFAULT_NAME_2__'
            ),
        ]);

        return new ContentTypeUpdateStep(
            $metadata,
            $fieldDefinitionCollection,
            new Matcher(Matcher::CONTENT_TYPE_IDENTIFIER, 'article'),
        );
    }

    private function createStepWithoutRequiredAndPositionDefinedInField(): StepInterface
    {
        $metadata = UpdateMetadata::createFromArray([
            'identifier' => 'article',
            'mainTranslation' => 'eng-GB',
            'modifierId' => 14,
            'modificationDate' => '2020-12-11T08:26:43+00:00',
            'remoteId' => '__REMOTE_ID__',
            'urlAliasSchema' => '__URL_ALIAS_SCHEMA__',
            'nameSchema' => '__NAME_SCHEMA__',
            'container' => false,
            'defaultAlwaysAvailable' => true,
            'defaultSortField' => self::DEFAULT_SORT_FIELD,
            'defaultSortOrder' => self::DEFAULT_SORT_ORDER,
            'translations' => [
                'eng-GB' => [
                    'name' => 'Blog',
                    'description' => '__DESCRIPTION__',
                ],
            ],
        ]);

        $fieldDefinitionCollection = FieldDefinitionCollection::create([
            FieldDefinition::fromAPIFieldDefinition(
                new ApiFieldDefinition([
                    'identifier' => 'title',
                    'fieldTypeIdentifier' => 'ezstring',
                    'fieldGroup' => '__FIELD_GROUP__',
                    'isRequired' => null,
                    'position' => null,
                ]),
                '__NEW_DEFAULT_NAME_2__'
            ),
            FieldDefinition::fromAPIFieldDefinition(
                new ApiFieldDefinition([
                    'identifier' => '__NEW_FIELD__',
                    'fieldTypeIdentifier' => 'ezstring',
                    'position' => self::FIELD_POSITION,
                    'fieldGroup' => '__FIELD_GROUP__',
                    'isRequired' => true,
                    'isSearchable' => false,
                    'isTranslatable' => false,
                    'isThumbnail' => false,
                    'isInfoCollector' => true,
                    'validatorConfiguration' => [
                        'StringLengthValidator' => [
                            'maxStringLength' => self::MAX_STRING_LENGTH,
                            'minStringLength' => null,
                        ],
                    ],
                ]),
                '__NEW_DEFAULT_FIELD_NAME__'
            ),
        ]);

        return new ContentTypeUpdateStep(
            $metadata,
            $fieldDefinitionCollection,
            new Matcher(Matcher::CONTENT_TYPE_IDENTIFIER, 'article'),
            []
        );
    }

    private function createStepNewField(): StepInterface
    {
        $metadata = UpdateMetadata::createFromArray([
            'identifier' => 'article',
            'mainTranslation' => 'eng-GB',
            'modifierId' => 14,
            'modificationDate' => '2020-12-11T08:26:43+00:00',
            'remoteId' => '__REMOTE_ID__',
            'urlAliasSchema' => '__URL_ALIAS_SCHEMA__',
            'nameSchema' => '__NAME_SCHEMA__',
            'container' => false,
            'defaultAlwaysAvailable' => true,
            'defaultSortField' => self::DEFAULT_SORT_FIELD,
            'defaultSortOrder' => self::DEFAULT_SORT_ORDER,
            'translations' => [
                'eng-GB' => [
                    'name' => 'Blog',
                    'description' => '__DESCRIPTION__',
                ],
            ],
        ]);

        $fieldDefinitionCollection = FieldDefinitionCollection::create([
            FieldDefinition::fromAPIFieldDefinition(
                new ApiFieldDefinition([
                    'identifier' => '__NEW_FIELD__',
                    'fieldTypeIdentifier' => 'ezstring',
                    'position' => self::FIELD_POSITION,
                    'fieldGroup' => '__FIELD_GROUP__',
                    'isRequired' => true,
                    'isSearchable' => false,
                    'isTranslatable' => false,
                    'isThumbnail' => false,
                    'isInfoCollector' => true,
                    'validatorConfiguration' => [
                        'StringLengthValidator' => [
                            'maxStringLength' => self::MAX_STRING_LENGTH,
                            'minStringLength' => null,
                        ],
                    ],
                ]),
                '__NEW_DEFAULT_FIELD_NAME__'
            ),
        ]);

        return new ContentTypeUpdateStep(
            $metadata,
            $fieldDefinitionCollection,
            new Matcher(Matcher::CONTENT_TYPE_IDENTIFIER, 'article'),
            []
        );
    }

    private function createStepWithChangedFieldType(): StepInterface
    {
        $metadata = UpdateMetadata::createFromArray([
            'identifier' => 'article',
            'mainTranslation' => 'eng-GB',
            'urlAliasSchema' => '__URL_ALIAS_SCHEMA__',
        ]);

        $fieldDefinitionCollection = FieldDefinitionCollection::create([
            FieldDefinition::fromAPIFieldDefinition(
                new ApiFieldDefinition([
                    'identifier' => 'title',
                    'fieldTypeIdentifier' => 'ezselection',
                    'isRequired' => null,
                    'position' => null,
                ]),
                '__NEW_DEFAULT_NAME_2__'
            ),
        ]);

        return new ContentTypeUpdateStep(
            $metadata,
            $fieldDefinitionCollection,
            new Matcher(Matcher::CONTENT_TYPE_IDENTIFIER, 'article'),
            []
        );
    }
}

class_alias(ContentTypeUpdateStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\ContentTypeUpdateStepExecutorTest');
