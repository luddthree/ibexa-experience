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
final class MigrateCommandContentTypeUpdateWithUpdateIdentifierTest extends AbstractMigrateCommandContentTypeUpdateTest
{
    private const FIELD_IDENTIFIER_TITLE = 'title';
    private const FIELD_IDENTIFIER_IMAGE = 'image';
    private const NEW_FIELD_IDENTIFIER_TITLE = 'title_new';
    private const NEW_FIELD_IDENTIFIER_IMAGE = '__image__';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content_type-update-with-update-field-identifier.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $contentTypeService = self::getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier('article');

        self::assertTrue($contentType->hasFieldDefinition(self::FIELD_IDENTIFIER_TITLE));
        self::assertTrue($contentType->hasFieldDefinition(self::FIELD_IDENTIFIER_IMAGE));
    }

    protected function postCommandAssertions(): void
    {
        $contentTypeService = self::getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier('article');

        self::assertTrue($contentType->hasFieldDefinition(self::NEW_FIELD_IDENTIFIER_TITLE));
        self::assertTrue($contentType->hasFieldDefinition(self::NEW_FIELD_IDENTIFIER_IMAGE));

        self::assertFalse($contentType->hasFieldDefinition(self::FIELD_IDENTIFIER_TITLE));
        self::assertFalse($contentType->hasFieldDefinition(self::FIELD_IDENTIFIER_IMAGE));
    }
}
