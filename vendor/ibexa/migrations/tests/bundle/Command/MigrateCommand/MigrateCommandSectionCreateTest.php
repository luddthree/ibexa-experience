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
final class MigrateCommandSectionCreateTest extends AbstractMigrateCommand
{
    private const SECTION_IDENTIFIER = 'section_1';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/section-create.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $sectionService = self::getSectionService();
        $found = true;

        try {
            $sectionService->loadSectionByIdentifier(self::SECTION_IDENTIFIER);
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertFalse($found);
    }

    protected function postCommandAssertions(): void
    {
        $sectionService = self::getSectionService();
        $sectionService->loadSectionByIdentifier(self::SECTION_IDENTIFIER);
    }
}

class_alias(MigrateCommandSectionCreateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandSectionCreateTest');
