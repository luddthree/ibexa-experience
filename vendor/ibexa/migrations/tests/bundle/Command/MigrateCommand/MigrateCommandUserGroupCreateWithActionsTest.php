<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandUserGroupCreateWithActionsTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/user-group-create-with-actions.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::assertContentRemoteIdNotExists('__with_identifier__');
        self::assertContentRemoteIdNotExists('__with_id__');
    }

    protected function postCommandAssertions(): void
    {
        $contentService = self::getContentService();
        $content = $contentService->loadContentByRemoteId('__with_identifier__');

        $userService = self::getUserService();

        self::assertTrue($userService->isUserGroup($content));
        $content = $contentService->loadContentByRemoteId('__with_id__');

        self::assertTrue($userService->isUserGroup($content));
        $userGroup = $userService->loadUserGroup($content->id);

        $roleService = self::getRoleService();
        $roleAssignments = $roleService->getRoleAssignmentsForUserGroup($userGroup);
        if ($roleAssignments instanceof Traversable) {
            $roleAssignments = iterator_to_array($roleAssignments);
        }
        self::assertCount(1, $roleAssignments);
        self::assertNotNull($roleAssignments[0]->limitation);
    }
}

class_alias(MigrateCommandUserGroupCreateWithActionsTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandUserGroupCreateWithActionsTest');
