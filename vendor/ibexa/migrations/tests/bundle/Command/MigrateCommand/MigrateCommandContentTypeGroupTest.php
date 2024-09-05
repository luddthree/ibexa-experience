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
final class MigrateCommandContentTypeGroupTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/1605771542_4936_content_type_group.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $contentTypeService = self::getContentTypeService();
        $found = true;

        try {
            $contentTypeService->loadContentTypeGroupByIdentifier('Baz');
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertFalse($found);
    }

    protected function postCommandAssertions(): void
    {
        $contentTypeService = self::getContentTypeService();
        $contentTypeService->loadContentTypeGroupByIdentifier('Baz');
    }
}

class_alias(MigrateCommandContentTypeGroupTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandContentTypeGroupTest');
