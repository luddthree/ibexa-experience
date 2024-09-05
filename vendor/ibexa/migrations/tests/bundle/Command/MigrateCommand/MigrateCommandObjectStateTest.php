<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandObjectStateTest extends AbstractMigrateCommand
{
    private const OBJECT_STATE_GROUP = 2;

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/object-state.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $objectStateGroup = self::getObjectStateService()->loadObjectStateGroup(self::OBJECT_STATE_GROUP);

        $this->assertObjectStateNotFound($objectStateGroup, 'state_1');
        $this->assertObjectStateNotFound($objectStateGroup, 'state_2');
    }

    private function assertObjectStateNotFound(
        ObjectStateGroup $objectStateGroup,
        string $objectStateIdentifier
    ): void {
        $found = true;

        try {
            self::getObjectStateService()->loadObjectStateByIdentifier($objectStateGroup, $objectStateIdentifier);
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertFalse($found);
    }

    protected function postCommandAssertions(): void
    {
        $objectStateGroup = self::getObjectStateService()->loadObjectStateGroup(self::OBJECT_STATE_GROUP);

        $this->assertObjectStateFound($objectStateGroup, 'state_1');
        $this->assertObjectStateFound($objectStateGroup, 'state_2');
    }

    private function assertObjectStateFound(
        ObjectStateGroup $objectStateGroup,
        string $objectStateIdentifier
    ): void {
        self::getObjectStateService()->loadObjectStateByIdentifier($objectStateGroup, $objectStateIdentifier);
    }
}

class_alias(MigrateCommandObjectStateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandObjectStateTest');
