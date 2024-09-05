<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandLocationUpdateTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/location-update.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $locationService = self::getLocationService();

        $this->preAssertWithRemoteId($locationService);
        $this->preAssertWithLocationId($locationService);
    }

    protected function postCommandAssertions(): void
    {
        $locationService = self::getLocationService();

        $this->postAssertWithRemoteId($locationService);
        $this->postAssertWithLocationId($locationService);
    }

    private function preAssertWithRemoteId(LocationService $locationService): void
    {
        $location = $locationService->loadLocationByRemoteId('f3e90596361e31d496d4026eb624c983');

        $this->assertEquals(
            Location::SORT_FIELD_PRIORITY,
            $location->sortField
        );

        $this->assertEquals(
            0,
            $location->priority
        );
    }

    private function preAssertWithLocationId(LocationService $locationService): void
    {
        $location = $locationService->loadLocation(5);

        $this->assertEquals(
            Location::SORT_FIELD_PATH,
            $location->sortField
        );

        $this->assertEquals(
            0,
            $location->priority
        );
    }

    private function postAssertWithRemoteId(LocationService $locationService): void
    {
        $location = $locationService->loadLocationByRemoteId('f3e90596361e31d496d4026eb624c983');

        $this->assertEquals(
            Location::SORT_FIELD_NAME,
            $location->sortField
        );

        $this->assertEquals(
            1,
            $location->priority
        );
    }

    private function postAssertWithLocationId(LocationService $locationService): void
    {
        $location = $locationService->loadLocation(5);

        $this->assertEquals(
            Location::SORT_FIELD_NAME,
            $location->sortField
        );

        $this->assertEquals(
            1,
            $location->priority
        );

        $this->assertEquals(
            '3f6d92f8044aed134f32153517850f5a',
            $location->remoteId
        );
    }
}

class_alias(MigrateCommandLocationUpdateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandLocationUpdateTest');
