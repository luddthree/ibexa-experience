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
final class MigrateCommandContentCreateWithAssignObjectStateActionTest extends AbstractMigrateCommandContentCreateTest
{
    private const KNOWN_REMOTE_ID = 'fooBar';
    private const KNOWN_OBJECT_STATE_GROUP = 'ez_lock';
    private const KNOWN_OBJECT_STATE = 'locked';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content-create-with-assign-object-state.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::assertContentRemoteIdNotExists(self::KNOWN_REMOTE_ID);
    }

    protected function postCommandAssertions(): void
    {
        $content = self::assertContentRemoteIdExists(self::KNOWN_REMOTE_ID);
        $objectStateGroup = self::getObjectStateService()->loadObjectStateGroupByIdentifier(self::KNOWN_OBJECT_STATE_GROUP);
        $objectState = self::getObjectStateService()->getContentState($content->contentInfo, $objectStateGroup);

        self::assertEquals($objectState->identifier, self::KNOWN_OBJECT_STATE);
    }
}
