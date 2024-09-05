<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandContentTypeUpdateTest extends AbstractMigrateCommandContentTypeUpdateTest
{
    private const FIELD_IDENTIFIER_IMAGE = 'image';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content_type-update.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $contentTypeService = self::getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier('article');
        $imageField = $contentType->fieldDefinitions->get(self::FIELD_IDENTIFIER_IMAGE);

        self::assertSame(1343140534, $contentType->modificationDate->getTimestamp());
        self::assertArticleMetadataRemainsUnchanged($contentType);
        self::assertNotSame('Blog', $contentType->getName());
        self::assertNotSame('__DESCRIPTION__', $contentType->getDescription());

        self::assertFalse($imageField->isThumbnail);

        self::assertSameContentTypeGroupIdentifiers(['Content'], $contentType->contentTypeGroups);
    }

    protected function postCommandAssertions(): void
    {
        $contentTypeService = self::getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier('article');

        self::assertGreaterThan(1596296154, $contentType->modificationDate->getTimestamp());
        self::assertSame('__REMOTE_ID__', $contentType->remoteId);
        self::assertSame('__URL_ALIAS__', $contentType->urlAliasSchema);
        self::assertSame('__NAME_SCHEMA__', $contentType->nameSchema);
        self::assertTrue($contentType->defaultAlwaysAvailable);
        self::assertSame(2, $contentType->defaultSortField);
        self::assertSame(2, $contentType->defaultSortOrder);
        self::assertSame('Blog', $contentType->getName());
        self::assertSame('__DESCRIPTION__', $contentType->getDescription());

        $titleField = $contentType->fieldDefinitions->get('title');
        self::assertSame(2, $titleField->position);
        self::assertFalse($titleField->isRequired);
        self::assertFalse($titleField->isSearchable);
        self::assertTrue($titleField->isInfoCollector);
        self::assertFalse($titleField->isTranslatable);
        self::assertFalse($titleField->isThumbnail);
        self::assertSame('__NEW_DEFAULT_NAME__', (string) $titleField->defaultValue);
        self::assertSame(128, $titleField->validatorConfiguration['StringLengthValidator']['maxStringLength']);

        $nameField = $contentType->fieldDefinitions->get('new_name');
        self::assertSame(0, $nameField->position);
        self::assertFalse($nameField->isRequired);
        self::assertFalse($nameField->isSearchable);
        self::assertFalse($nameField->isInfoCollector);
        self::assertFalse($nameField->isTranslatable);
        self::assertFalse($nameField->isThumbnail);

        $imageField = $contentType->fieldDefinitions->get(self::FIELD_IDENTIFIER_IMAGE);
        self::assertTrue($imageField->isThumbnail);

        self::assertSameContentTypeGroupIdentifiers(['Media'], $contentType->contentTypeGroups);
    }
}

class_alias(MigrateCommandContentTypeUpdateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandContentTypeUpdateTest');
