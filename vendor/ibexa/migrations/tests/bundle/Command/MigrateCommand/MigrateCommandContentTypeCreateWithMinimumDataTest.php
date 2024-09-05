<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandContentTypeCreateWithMinimumDataTest extends AbstractMigrateCommand
{
    private const FIELD_IDENTIFIER_STRING = 'title';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content_type-create-minimum-data.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $contentTypeService = self::getContentTypeService();
        $found = true;

        try {
            $contentTypeService->loadContentTypeByIdentifier('foo');
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertFalse($found);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    protected function postCommandAssertions(): void
    {
        $contentTypeService = self::getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier('foo_minimal');
        $titleField = $contentType->fieldDefinitions->get(self::FIELD_IDENTIFIER_STRING);

        self::assertEquals('ezstring', $titleField->fieldTypeIdentifier);
    }
}
