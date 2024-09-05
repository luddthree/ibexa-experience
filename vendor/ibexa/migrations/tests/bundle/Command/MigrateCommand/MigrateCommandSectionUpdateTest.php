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
final class MigrateCommandSectionUpdateTest extends AbstractMigrateCommand
{
    private const SECTION_IDENTIFIER = 'standard';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/section-update.yaml');
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

        self::assertTrue($found);
    }

    protected function postCommandAssertions(): void
    {
        $sectionService = self::getSectionService();
        $section = $sectionService->loadSectionByIdentifier('__NEW_IDENTIFIER__');

        self::assertEquals('__NEW_IDENTIFIER__', $section->identifier);
        self::assertEquals('__NEWER_NAME__', $section->name);
    }
}

class_alias(MigrateCommandSectionUpdateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandSectionUpdateTest');
