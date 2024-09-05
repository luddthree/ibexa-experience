<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Core\Repository\Values\User\UserReference;
use Ibexa\Tests\Bundle\Migration\Command\AbstractCommandTest;
use Ibexa\Tests\Migration\FileLoadTrait;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
abstract class AbstractMigrateCommand extends AbstractCommandTest
{
    use FileLoadTrait;

    private const FIXTURE_ADMIN_ID = 14;

    final protected static function getCommandName(): string
    {
        return 'ibexa:migrations:migrate';
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::getPermissionResolver()->setCurrentUserReference(new UserReference(self::FIXTURE_ADMIN_ID));
    }

    final public function testMigrating(): void
    {
        $flysystemAdapter = self::getFilesystem();

        $contents = $this->getTestContent();
        $flysystemAdapter->write('migrations/' . $this->getFilename(), $contents);

        self::assertCount(0, self::getMetadataStorage()->getExecutedMigrations());

        $this->preCommandAssertions();

        $commandInput = $this->buildCommandInput();
        $this->commandTester->execute($commandInput);

        self::assertSame(0, $this->commandTester->getStatusCode());
        self::assertCount(1, self::getMetadataStorage()->getExecutedMigrations());

        $this->postCommandAssertions();
    }

    protected function getFilename(): string
    {
        return 'foo.yaml';
    }

    abstract protected function getTestContent(): string;

    abstract protected function preCommandAssertions(): void;

    abstract protected function postCommandAssertions(): void;

    /**
     * @return array<string, scalar|scalar[]>
     */
    protected function buildCommandInput(): array
    {
        return [
            '--file' => [$this->getFilename()],
        ];
    }

    protected static function assertContentRemoteIdExists(string $remoteId): Content
    {
        $content = self::attemptContentLoad($remoteId);
        self::assertNotNull($content, sprintf('Content with %s remote ID does not exist.', $remoteId));

        return $content;
    }

    protected static function assertContentRemoteIdNotExists(string $remoteId): void
    {
        $content = self::attemptContentLoad($remoteId);
        self::assertNull($content, sprintf('Content with %s remote ID exists.', $remoteId));
    }

    private static function attemptContentLoad(string $remoteId): ?Content
    {
        try {
            return self::getContentService()->loadContentByRemoteId($remoteId);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    protected static function assertUserGroup(Content $content): UserGroup
    {
        $userService = self::getUserService();

        self::assertTrue($userService->isUserGroup($content));

        return $userService->loadUserGroup($content->id);
    }

    /**
     * @param string[] $checkRoleIdentifiers
     */
    protected static function assertUserGroupHasRoles(UserGroup $userGroup, array $checkRoleIdentifiers): UserGroup
    {
        $roleService = self::getRoleService();

        $roleAssignments = $roleService->getRoleAssignmentsForUserGroup($userGroup);

        /** @var string[] $roleIdentifiers */
        $roleIdentifiers = [];
        foreach ($roleAssignments as $key => $roleAssignment) {
            $roleIdentifiers[$key] = $roleAssignment->getRole()->identifier;
        }

        self::assertEquals($checkRoleIdentifiers, $roleIdentifiers);

        return $userGroup;
    }
}

class_alias(AbstractMigrateCommand::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\AbstractMigrateCommand');
