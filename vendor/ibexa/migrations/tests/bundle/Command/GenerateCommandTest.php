<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Ibexa\Tests\Migration\FileLoadTrait;
use InvalidArgumentException;
use League\Flysystem\FilesystemOperator;

/**
 * @covers \Ibexa\Bundle\Migration\Command\GenerateCommand
 *
 * @phpstan-type TGenerateTestOptions array{
 *     check_only_beginning?: bool,
 *     database_specific_result?: bool|array<string>,
 * }
 */
final class GenerateCommandTest extends AbstractCommandTest
{
    use FileLoadTrait;

    private const EDITOR_ROLE_FIXTURE_ID = 3;

    private const OPTION_DATABASE_SPECIFIC_RESULT = 'database_specific_result';
    private const OPTION_CHECK_ONLY_BEGINNING = 'check_only_beginning';

    private const DATABASE_POSTGRES = 'postgres';
    private const DATABASE_MYSQL = 'mysql';
    private const DATABASE_SQLITE = 'sqlite';

    private FilesystemOperator $flysystem;

    protected static function getCommandName(): string
    {
        return 'ibexa:migrations:generate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->flysystem = self::getFilesystem();
    }

    /**
     * @dataProvider provideContentCommandData
     * @dataProvider provideContentTypeCommandData
     * @dataProvider provideRoleCommandData
     * @dataProvider provideContentTypeGroupCommandData
     * @dataProvider provideUserCommandData
     * @dataProvider provideUserGroupCommandData
     * @dataProvider provideObjectStateCommandData
     * @dataProvider provideObjectStateGroupCommandData
     * @dataProvider provideLanguageCommandData
     * @dataProvider provideSectionCommandData
     * @dataProvider provideLocationCommandData
     *
     * @param array<string, scalar|scalar[]> $input
     *
     * @phpstan-param TGenerateTestOptions $options
     *
     * @throws \Exception
     */
    public function testBaseExecution(
        array $input,
        string $expectedFile,
        array $options = []
    ): void {
        $checkOnlyBeginning = $options[self::OPTION_CHECK_ONLY_BEGINNING] ?? false;
        $databaseSpecificResult = $options[self::OPTION_DATABASE_SPECIFIC_RESULT] ?? false;

        self::assertCount(0, $this->flysystem->listContents(''));
        $this->commandTester->execute($input);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Done!', $output);
        $filesystemContents = iterator_to_array($this->flysystem->listContents('migrations/'));
        self::assertCount(1, $filesystemContents);
        $file = $filesystemContents[0];
        $generatedContents = $this->flysystem->read($file->path());
        self::assertIsString($generatedContents);

        $expectedFile = $this->getExpectedFileLocation($expectedFile, $databaseSpecificResult);

        if ($checkOnlyBeginning) {
            self::assertBeginningOfOutputMatchesFile($expectedFile, $generatedContents);
        } else {
            self::assertOutputEqualsFile($expectedFile, $generatedContents);
        }
    }

    /**
     * @dataProvider provideInputWithMatchPropertyButWithoutValue
     *
     * @param array<string, scalar|scalar[]> $input
     */
    public function testMatchPropertyAndMatchValueRequireEachOther(array $input): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"--value" option is required when using "--match-property"');
        $this->commandTester->execute($input);
    }

    /**
     * @return iterable<array{array<string, scalar|scalar[]>}>
     */
    public function provideInputWithMatchPropertyButWithoutValue(): iterable
    {
        foreach ($this->getAllTypeModeCombinations() as $name => $combination) {
            $type = $combination['type'];
            $mode = $combination['mode'];
            yield $name => [
                [
                    '--type' => $type,
                    '--mode' => $mode,
                    '--match-property' => '__any__',
                ],
            ];
        }
    }

    /**
     * @return iterable<array{type: string, mode: string}>
     */
    private function getAllTypeModeCombinations(): iterable
    {
        return [
            'content create' => ['type' => 'content', 'mode' => 'create'],
            'content update' => ['type' => 'content', 'mode' => 'update'],
            'content delete' => ['type' => 'content', 'mode' => 'delete'],
            'content_type create' => ['type' => 'content_type', 'mode' => 'create'],
            'content_type update' => ['type' => 'content_type', 'mode' => 'update'],
            'role create' => ['type' => 'role', 'mode' => 'create'],
            'role update' => ['type' => 'role', 'mode' => 'update'],
            'role delete' => ['type' => 'role', 'mode' => 'delete'],
            'content_type_group create' => ['type' => 'content_type_group', 'mode' => 'create'],
            'content_type_group update' => ['type' => 'content_type_group', 'mode' => 'update'],
            'content_type_group delete' => ['type' => 'content_type_group', 'mode' => 'delete'],
            'user create' => ['type' => 'user', 'mode' => 'create'],
            'user update' => ['type' => 'user', 'mode' => 'update'],
            'user_group create' => ['type' => 'user_group', 'mode' => 'create'],
            'user_group delete' => ['type' => 'user_group', 'mode' => 'delete'],
            'section create' => ['type' => 'section', 'mode' => 'create'],
            'section update' => ['type' => 'section', 'mode' => 'update'],
        ];
    }

    private static function assertOutputEqualsFile(string $expectedFile, string $generatedContents): void
    {
        self::assertStringEqualsFile(
            $expectedFile,
            $generatedContents,
            "Failed asserting snapshot file \"file://$expectedFile\" matches output"
        );
    }

    private static function assertBeginningOfOutputMatchesFile(string $expectedFile, string $generatedContents): void
    {
        $expectedContents = self::loadFile($expectedFile);
        self::assertStringStartsWith(
            $expectedContents,
            $generatedContents,
            "Failed asserting that beginning of snapshot file \"file://$expectedFile\" matches output"
        );
    }

    /**
     * @return iterable<string, array{array<string, scalar|scalar[]>, string}>
     */
    public static function provideContentCommandData(): iterable
    {
        yield 'Content' => [
            [
                '--type' => 'content',
                '--mode' => 'create',
                '--value' => ['*'],
            ],
            'content.yaml',
        ];

        yield 'Specific Content' => [
            [
                '--type' => 'content',
                '--mode' => 'create',
                '--match-property' => 'content_type_identifier',
                '--value' => ['landing_page'],
            ],
            'specific-content.yaml',
        ];

        yield 'Content Update' => [
            [
                '--type' => 'content',
                '--mode' => 'update',
                '--value' => ['*'],
            ],
            'content-update.yaml',
        ];

        yield 'Specific Content Update' => [
            [
                '--type' => 'content',
                '--mode' => 'update',
                '--match-property' => 'content_type_identifier',
                '--value' => ['landing_page'],
            ],
            'specific-content-update.yaml',
        ];

        yield 'Content Delete' => [
            [
                '--type' => 'content',
                '--mode' => 'delete',
            ],
            'content-delete.yaml',
        ];
    }

    /**
     * @return iterable<string, array{array<string, scalar|scalar[]>, string, 2?: TGenerateTestOptions}>
     */
    public static function provideContentTypeCommandData(): iterable
    {
        yield 'Create content type' => [
            [
                '--type' => 'content_type',
                '--mode' => 'create',
                '--value' => ['*'],
            ],
            'content-type.yaml',
            [self::OPTION_CHECK_ONLY_BEGINNING => true],
        ];

        yield 'Create content type by content_type_identifier' => [
            [
                '--type' => 'content_type',
                '--mode' => 'create',
                '--match-property' => 'content_type_identifier',
                '--value' => ['article'],
            ],
            'specific-content-type.yaml',
        ];

        yield 'Update content type' => [
            [
                '--type' => 'content_type',
                '--mode' => 'update',
                '--value' => ['*'],
            ],
            'content-type-update.yaml',
            [self::OPTION_CHECK_ONLY_BEGINNING => true],
        ];

        yield 'Update content type by content_type_identifier' => [
            [
                '--type' => 'content_type',
                '--mode' => 'update',
                '--match-property' => 'content_type_identifier',
                '--value' => ['article'],
            ],
            'specific-content-type-update.yaml',
        ];
    }

    /**
     * @return iterable<string, array{array<string, scalar|scalar[]>, string}>
     */
    public static function provideRoleCommandData(): iterable
    {
        yield 'Create Roles' => [
            [
                '--type' => 'role',
                '--mode' => 'create',
            ],
            'roles-create.yaml',
            [self::OPTION_DATABASE_SPECIFIC_RESULT => [self::DATABASE_POSTGRES]],
        ];

        yield 'Create Roles by ID' => [
            [
                '--type' => 'role',
                '--mode' => 'create',
                '--match-property' => 'id',
                '--value' => [(string) self::EDITOR_ROLE_FIXTURE_ID],
            ],
            'roles-create-specific.yaml',
            [self::OPTION_DATABASE_SPECIFIC_RESULT => true],
        ];

        yield 'Create Roles by identifier' => [
            [
                '--type' => 'role',
                '--mode' => 'create',
                '--match-property' => 'identifier',
                '--value' => ['Editor'],
            ],
            'roles-create-specific.yaml',
            [self::OPTION_DATABASE_SPECIFIC_RESULT => true],
        ];

        yield 'Update Roles' => [
            [
                '--type' => 'role',
                '--mode' => 'update',
            ],
            'roles-update.yaml',
            [self::OPTION_DATABASE_SPECIFIC_RESULT => [self::DATABASE_POSTGRES]],
        ];

        yield 'Update Roles by ID' => [
            [
                '--type' => 'role',
                '--mode' => 'update',
                '--match-property' => 'id',
                '--value' => [(string) self::EDITOR_ROLE_FIXTURE_ID],
            ],
            'roles-update-specific.yaml',
            [self::OPTION_DATABASE_SPECIFIC_RESULT => true],
        ];

        yield 'Update Roles by identifier' => [
            [
                '--type' => 'role',
                '--mode' => 'update',
                '--match-property' => 'identifier',
                '--value' => ['Editor'],
            ],
            'roles-update-specific.yaml',
            [self::OPTION_DATABASE_SPECIFIC_RESULT => true],
        ];

        yield 'Delete Roles' => [
            [
                '--type' => 'role',
                '--mode' => 'delete',
            ],
            'roles-delete.yaml',
        ];

        yield 'Delete Roles by ID' => [
            [
                '--type' => 'role',
                '--mode' => 'delete',
                '--match-property' => 'id',
                '--value' => [(string) self::EDITOR_ROLE_FIXTURE_ID],
            ],
            'roles-delete-specific.yaml',
        ];

        yield 'Delete Roles by identifier' => [
            [
                '--type' => 'role',
                '--mode' => 'delete',
                '--match-property' => 'identifier',
                '--value' => ['Editor'],
            ],
            'roles-delete-specific.yaml',
        ];
    }

    /**
     * @return iterable<string, array{array<string, scalar|scalar[]>, string, 2?: TGenerateTestOptions}>
     */
    public static function provideContentTypeGroupCommandData(): iterable
    {
        yield 'Content type Group' => [
            [
                '--type' => 'content_type_group',
                '--mode' => 'create',
                '--value' => ['*'],
            ],
            'content-type-group.yaml',
            [self::OPTION_CHECK_ONLY_BEGINNING => true],
        ];

        yield 'Specific content type Group' => [
            [
                '--type' => 'content_type_group',
                '--mode' => 'create',
                '--match-property' => 'content_type_group_identifier',
                '--value' => ['Media'],
            ],
            'specific-content-type-group.yaml',
        ];

        yield 'System content type Group' => [
            [
                '--type' => 'content_type_group',
                '--mode' => 'create',
                '--match-property' => 'content_type_group_identifier',
                '--value' => ['System'],
            ],
            'system-content-type-group.yaml',
        ];
    }

    /**
     * @return iterable<string, array{array<string, scalar|scalar[]>, string}>
     */
    public static function provideUserCommandData(): iterable
    {
        yield 'User create' => [
            [
                '--type' => 'user',
                '--mode' => 'create',
            ],
            'create-user.yaml',
        ];

        yield 'User create with wildcard' => [
            [
                '--type' => 'user',
                '--mode' => 'create',
                '--value' => ['*'],
            ],
            'create-user.yaml',
        ];

        yield 'Specific user create ' => [
            [
                '--type' => 'user',
                '--mode' => 'create',
                '--match-property' => 'login',
                '--value' => ['anonymous'],
            ],
            'create-user-specific.yaml',
        ];

        yield 'User update' => [
            [
                '--type' => 'user',
                '--mode' => 'update',
            ],
            'update-user.yaml',
        ];

        yield 'User update with wildcard' => [
            [
                '--type' => 'user',
                '--mode' => 'update',
                '--value' => ['*'],
            ],
            'update-user.yaml',
        ];

        yield 'Specific user update ' => [
            [
                '--type' => 'user',
                '--mode' => 'update',
                '--match-property' => 'login',
                '--value' => ['anonymous'],
            ],
            'update-user-specific.yaml',
        ];
    }

    /**
     * @return iterable<string, array{array<string, scalar|scalar[]>, string}>
     */
    public static function provideUserGroupCommandData(): iterable
    {
        yield 'User Group create' => [
            [
                '--type' => 'user_group',
                '--mode' => 'create',
            ],
            'create-user-group.yaml',
            [self::OPTION_DATABASE_SPECIFIC_RESULT => true],
        ];

        yield 'User Group create with wildcard' => [
            [
                '--type' => 'user_group',
                '--mode' => 'create',
                '--value' => ['*'],
            ],
            'create-user-group.yaml',
            [self::OPTION_DATABASE_SPECIFIC_RESULT => true],
        ];

        yield 'User Group update' => [
            [
                '--type' => 'user_group',
                '--mode' => 'update',
            ],
            'update-user-group.yaml',
            [self::OPTION_DATABASE_SPECIFIC_RESULT => true],
        ];

        yield 'User Group update with wildcard' => [
            [
                '--type' => 'user_group',
                '--mode' => 'update',
                '--value' => ['*'],
            ],
            'update-user-group.yaml',
            [self::OPTION_DATABASE_SPECIFIC_RESULT => true],
        ];

        yield 'User Group delete with wildcard' => [
            [
                '--type' => 'user_group',
                '--mode' => 'delete',
                '--value' => ['*'],
            ],
            'delete-user-group.yaml',
            [self::OPTION_DATABASE_SPECIFIC_RESULT => true],
        ];

        yield 'User Group delete' => [
            [
                '--type' => 'user_group',
                '--mode' => 'delete',
            ],
            'delete-user-group.yaml',
            [self::OPTION_DATABASE_SPECIFIC_RESULT => true],
        ];
    }

    /**
     * @return iterable<string, array{array<string, scalar|scalar[]>, string}>
     */
    public static function provideObjectStateCommandData(): iterable
    {
        yield 'Object State create' => [
            [
                '--type' => 'object_state',
                '--mode' => 'create',
            ],
            'create-object-state.yaml',
        ];
    }

    /**
     * @return iterable<string, array{array<string, scalar|scalar[]>, string}>
     */
    public static function provideObjectStateGroupCommandData(): iterable
    {
        yield 'Object State Group create' => [
            [
                '--type' => 'object_state_group',
                '--mode' => 'create',
            ],
            'create-object-state-group.yaml',
        ];
    }

    /**
     * @return iterable<string, array{array<string, scalar|scalar[]>, string}>
     */
    public static function provideLanguageCommandData(): iterable
    {
        yield 'Language create' => [
            [
                '--type' => 'language',
                '--mode' => 'create',
            ],
            'language-create.yaml',
        ];

        yield 'Language create by language code' => [
            [
                '--type' => 'language',
                '--mode' => 'create',
                '--match-property' => 'language_code',
                '--value' => ['ger-DE'],
            ],
            'language-create-by-language-code.yaml',
        ];
    }

    /**
     * @return iterable<string, array{array<string, scalar|scalar[]>, string}>
     */
    public static function provideSectionCommandData(): iterable
    {
        yield 'Section create' => [
            [
                '--type' => 'section',
                '--mode' => 'create',
            ],
            'create-section.yaml',
        ];

        yield 'Section update' => [
            [
                '--type' => 'section',
                '--mode' => 'update',
            ],
            'update-section.yaml',
        ];

        yield 'Section update with identifier' => [
            [
                '--type' => 'section',
                '--mode' => 'update',
                '--match-property' => 'section_identifier',
                '--value' => ['standard'],
            ],
            'update-section-by-identifier.yaml',
        ];

        yield 'Section update with id' => [
            [
                '--type' => 'section',
                '--mode' => 'update',
                '--match-property' => 'section_id',
                '--value' => [5],
            ],
            'update-section-by-id.yaml',
        ];
    }

    /**
     * @return iterable<string, array{array<string, scalar|scalar[]>, string}>
     */
    public static function provideLocationCommandData(): iterable
    {
        yield 'Location update' => [
            [
                '--type' => 'location',
                '--mode' => 'update',
            ],
            'location-update.yaml',
        ];

        yield 'Location update with remote ids' => [
            [
                '--type' => 'location',
                '--mode' => 'update',
                '--match-property' => 'location_remote_id',
                '--value' => ['f3e90596361e31d496d4026eb624c983', '3f6d92f8044aed134f32153517850f5a'],
            ],
            'location-update-by-remote-id.yaml',
        ];

        yield 'Location update with locations ids' => [
            [
                '--type' => 'location',
                '--mode' => 'update',
                '--match-property' => 'location_id',
                '--value' => [2, 5],
            ],
            'location-update-by-remote-id.yaml',
        ];
    }

    /**
     * @param string $expectedFile
     * @param bool|array<string> $databaseSpecificResult
     */
    private function getExpectedFileLocation(string $expectedFile, $databaseSpecificResult): string
    {
        $defaultLocation = __DIR__ . '/generate-command-results';
        if ($databaseSpecificResult === false) {
            return $defaultLocation . '/' . $expectedFile;
        }

        $connection = self::getContainer()->get('doctrine.dbal.default_connection');
        self::assertInstanceOf(Connection::class, $connection);
        $platform = $connection->getDatabasePlatform();
        self::assertNotNull($platform);

        if ($this->shouldUsePostgresSpecificResult($platform, $databaseSpecificResult)) {
            return $defaultLocation . '/postgres/' . $expectedFile;
        } elseif ($this->shouldUseMySQLSpecificResult($platform, $databaseSpecificResult)) {
            return $defaultLocation . '/mysql/' . $expectedFile;
        } elseif ($this->shouldUseSqliteSpecificResult($platform, $databaseSpecificResult)) {
            return $defaultLocation . '/sqlite/' . $expectedFile;
        }

        return $defaultLocation . '/' . $expectedFile;
    }

    /**
     * @param bool|array<string> $databaseSpecificResult
     */
    private function shouldUsePostgresSpecificResult(AbstractPlatform $platform, $databaseSpecificResult): bool
    {
        if (!$platform instanceof PostgreSQL94Platform) {
            return false;
        }

        if (is_bool($databaseSpecificResult)) {
            return $databaseSpecificResult;
        }

        return in_array(self::DATABASE_POSTGRES, $databaseSpecificResult, true);
    }

    /**
     * @param bool|array<string> $databaseSpecificResult
     */
    private function shouldUseMySQLSpecificResult(AbstractPlatform $platform, $databaseSpecificResult): bool
    {
        if (!$platform instanceof MySqlPlatform) {
            return false;
        }

        if (is_bool($databaseSpecificResult)) {
            return $databaseSpecificResult;
        }

        return in_array(self::DATABASE_MYSQL, $databaseSpecificResult, true);
    }

    /**
     * @param bool|array<string> $databaseSpecificResult
     */
    private function shouldUseSqliteSpecificResult(AbstractPlatform $platform, $databaseSpecificResult): bool
    {
        if (!$platform instanceof SqlitePlatform) {
            return false;
        }

        if (is_bool($databaseSpecificResult)) {
            return $databaseSpecificResult;
        }

        return in_array(self::DATABASE_SQLITE, $databaseSpecificResult, true);
    }
}

class_alias(GenerateCommandTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\GenerateCommandTest');
