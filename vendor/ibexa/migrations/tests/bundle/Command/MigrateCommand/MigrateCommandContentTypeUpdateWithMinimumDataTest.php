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
final class MigrateCommandContentTypeUpdateWithMinimumDataTest extends AbstractMigrateCommandContentTypeUpdateTest
{
    private const FIELD_IDENTIFIER_TITLE = 'title';
    private const CONTENT_TYPE_IDENTIFIER = 'article';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content_type-update-minimum-data.yaml');
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    protected function preCommandAssertions(): void
    {
        $contentTypeService = self::getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier(self::CONTENT_TYPE_IDENTIFIER);
        $titleField = $contentType->fieldDefinitions->get(self::FIELD_IDENTIFIER_TITLE);

        self::assertArticleMetadataRemainsUnchanged($contentType);
        self::assertSame(1, $titleField->position);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    protected function postCommandAssertions(): void
    {
        $contentTypeService = self::getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier(self::CONTENT_TYPE_IDENTIFIER);
        $titleField = $contentType->fieldDefinitions->get(self::FIELD_IDENTIFIER_TITLE);

        self::assertSame('__REMOTE_ID__', $contentType->remoteId);
        self::assertSame(5, $titleField->position);
    }
}
