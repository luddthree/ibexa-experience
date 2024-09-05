<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandContentUpdateByLocationRemoteIdTest extends AbstractMigrateCommand
{
    private const KNOWN_CONTENT_REMOTE_ID = '8a9c9c761004866fb458d89910f52bee';
    private const KNOWN_LOCATION_REMOTE_ID = 'f3e90596361e31d496d4026eb624c983';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content-update-with-location-remote-id.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $content = self::assertContentRemoteIdExists(self::KNOWN_CONTENT_REMOTE_ID);
        $location = $content->getVersionInfo()->getContentInfo()->getMainLocation();
        self::assertInstanceOf(Location::class, $location);
        self::assertSame(
            self::KNOWN_LOCATION_REMOTE_ID,
            $location->remoteId,
        );
        $field = $content->getField('name');
        self::assertNotNull($field);
        self::assertSame('Home', (string) $field->value);
    }

    protected function postCommandAssertions(): void
    {
        $content = self::assertContentRemoteIdExists(self::KNOWN_CONTENT_REMOTE_ID);
        $location = $content->getVersionInfo()->getContentInfo()->getMainLocation();
        self::assertInstanceOf(Location::class, $location);
        self::assertSame(
            self::KNOWN_LOCATION_REMOTE_ID,
            $location->remoteId,
        );
        $field = $content->getField('name');
        self::assertNotNull($field);
        self::assertSame('Partners', (string) $field->value);
    }
}

class_alias(MigrateCommandContentUpdateByLocationRemoteIdTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandContentUpdateByLocationRemoteIdTest');
