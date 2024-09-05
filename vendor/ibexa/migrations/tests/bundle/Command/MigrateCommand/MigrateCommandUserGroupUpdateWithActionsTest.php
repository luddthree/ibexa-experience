<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Core\Base\Exceptions\NotFoundException;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandUserGroupUpdateWithActionsTest extends AbstractMigrateCommand
{
    private const KNOWN_USER_GROUP_REMOTE_ID = '3c160cca19fb135f83bd02d911f04db2';

    /** @var int|null */
    private $roleAssignmentId = null;

    protected function getTestContent(): string
    {
        $fixtures = self::loadFile(__DIR__ . '/migrate-command-fixtures/user-group-update-with-actions.yaml');
        $roleAssignmentIds = $this->getRoleAssignmentIds();

        if (empty($roleAssignmentIds)) {
            throw new NotFoundException('user group role assignment', self::KNOWN_USER_GROUP_REMOTE_ID);
        }

        $this->roleAssignmentId = $roleAssignmentIds[0];

        return str_replace('__id__', (string)$this->roleAssignmentId, $fixtures);
    }

    protected function preCommandAssertions(): void
    {
        $roleAssignmentIds = $this->getRoleAssignmentIds();
        self::assertContains($this->roleAssignmentId, $roleAssignmentIds);
    }

    protected function postCommandAssertions(): void
    {
        $roleAssignmentIds = $this->getRoleAssignmentIds();
        self::assertNotContains($this->roleAssignmentId, $roleAssignmentIds);
    }

    /**
     * @return int[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getRoleAssignmentIds(): array
    {
        $userGroup = self::getUserService()->loadUserGroupByRemoteId(self::KNOWN_USER_GROUP_REMOTE_ID);
        $roleAssignments = self::getRoleService()->getRoleAssignmentsForUserGroup($userGroup);
        $roleAssignmentIds = [];

        foreach ($roleAssignments as $roleAssignment) {
            $roleAssignmentIds[] = $roleAssignment->id;
        }

        return $roleAssignmentIds;
    }
}
