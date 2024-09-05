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
final class MigrateCommandObjectStateGroupTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/object-state-group.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $objectStateService = self::getObjectStateService();
        $found = true;

        try {
            $objectStateService->loadObjectStateGroupByIdentifier('state_group_1');
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertFalse($found);
    }

    protected function postCommandAssertions(): void
    {
        $objectStateService = self::getObjectStateService();
        $objectStateService->loadObjectStateGroupByIdentifier('state_group_1');
    }
}

class_alias(MigrateCommandObjectStateGroupTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandObjectStateGroupTest');
