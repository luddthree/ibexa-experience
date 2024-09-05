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
final class MigrateCommandContentTypeUpdateRemoveFieldsPostActionTest extends AbstractMigrateCommandContentTypeUpdateTest
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content_type-update-remove-field.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $contentTypeService = self::getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier('article');

        self::assertSame(1343140534, $contentType->modificationDate->getTimestamp());
        self::assertArticleMetadataRemainsUnchanged($contentType);

        self::assertSameContentTypeGroupIdentifiers(['Content'], $contentType->contentTypeGroups);
        self::assertTrue($contentType->hasFieldDefinition('short_title'));
    }

    protected function postCommandAssertions(): void
    {
        $contentTypeService = self::getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier('article');

        self::assertGreaterThan(1596296154, $contentType->modificationDate->getTimestamp());
        self::assertArticleMetadataRemainsUnchanged($contentType);

        self::assertSameContentTypeGroupIdentifiers(['Media'], $contentType->contentTypeGroups);
        self::assertFalse($contentType->hasFieldDefinition('short_title'));
    }
}

class_alias(MigrateCommandContentTypeUpdateRemoveFieldsPostActionTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandContentTypeUpdateRemoveFieldsPostActionTest');
