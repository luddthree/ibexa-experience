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
final class MigrateCommandContentTypeCreateTest extends AbstractMigrateCommand
{
    private const FIELD_IDENTIFIER_IMAGE = 'image';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content_type-create.yaml');
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

    protected function postCommandAssertions(): void
    {
        $contentTypeService = self::getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier('foo');
        $imageField = $contentType->fieldDefinitions->get(self::FIELD_IDENTIFIER_IMAGE);

        self::assertTrue($imageField->isThumbnail);
    }
}

class_alias(MigrateCommandContentTypeCreateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandContentTypeCreateTest');
