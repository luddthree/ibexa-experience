<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

/**
 * @covers \Ibexa\Platform\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandLocationSwapTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/location-swap.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $locationService = self::getLocationService();

        $location = $locationService->loadLocationByRemoteId('f3e90596361e31d496d4026eb624c983');
        self::assertSame('/1/2/', $location->getPathString());
        self::assertSame('Home', $location->getContentInfo()->name);

        $location = $locationService->loadLocation(5);
        self::assertSame('/1/5/', $location->getPathString());
        self::assertSame('Users', $location->getContentInfo()->name);
    }

    protected function postCommandAssertions(): void
    {
        $locationService = self::getLocationService();

        $location = $locationService->loadLocationByRemoteId('f3e90596361e31d496d4026eb624c983');
        self::assertSame('/1/2/', $location->getPathString());
        self::assertSame('Users', $location->getContentInfo()->name);

        $location = $locationService->loadLocation(5);
        self::assertSame('/1/5/', $location->getPathString());
        self::assertSame('Home', $location->getContentInfo()->name);
    }
}
