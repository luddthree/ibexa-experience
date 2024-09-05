<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Migration\StepExecutor\LocationUpdateStepExecutor;
use Ibexa\Migration\ValueObject\Location\Matcher;
use Ibexa\Migration\ValueObject\Location\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\LocationUpdateStep;

/**
 * @covers \Ibexa\Migration\StepExecutor\LocationUpdateStepExecutor
 */
final class LocationUpdateStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    public function testHandleWithRemoteIdMatcher(): void
    {
        $locationService = self::getLocationService();

        $location = $locationService->loadLocationByRemoteId('f3e90596361e31d496d4026eb624c983');

        self::assertSame(Location::SORT_FIELD_PRIORITY, $location->sortField);
        self::assertSame(0, $location->priority);

        $step = new LocationUpdateStep(
            new UpdateMetadata(
                'f3e90596361e31d496d4026eb624c983',
                1,
                Location::SORT_FIELD_NAME,
                Location::SORT_ORDER_DESC,
            ),
            new Matcher(
                Matcher::LOCATION_REMOTE_ID,
                'f3e90596361e31d496d4026eb624c983'
            ),
        );

        $executor = new LocationUpdateStepExecutor(
            $locationService
        );

        $executor->handle($step);

        $locationUpdated = $locationService->loadLocationByRemoteId('f3e90596361e31d496d4026eb624c983');

        self::assertSame(Location::SORT_FIELD_NAME, $locationUpdated->sortField);
        self::assertSame(1, $locationUpdated->priority);
    }

    public function testHandleWithLocationIdMatcher(): void
    {
        $locationService = self::getLocationService();

        $location = $locationService->loadLocation(2);

        self::assertSame(Location::SORT_FIELD_PRIORITY, $location->sortField);
        self::assertSame(0, $location->priority);

        $step = new LocationUpdateStep(
            new UpdateMetadata(
                null,
                1,
                Location::SORT_FIELD_NAME,
                Location::SORT_ORDER_DESC,
            ),
            new Matcher(
                Matcher::LOCATION_ID,
                2
            ),
        );

        $executor = new LocationUpdateStepExecutor(
            $locationService
        );

        $executor->handle($step);

        $locationUpdated = $locationService->loadLocation(2);

        self::assertSame(Location::SORT_FIELD_NAME, $locationUpdated->sortField);
        self::assertSame(1, $locationUpdated->priority);
        self::assertSame('f3e90596361e31d496d4026eb624c983', $locationUpdated->remoteId);
    }
}

class_alias(LocationUpdateStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\LocationUpdateStepExecutorTest');
