<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationList;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandContentCreateWithAssignParentLocationActionTest extends AbstractMigrateCommandContentCreateTest
{
    private const KNOWN_LOCATION_ID = 2;

    /** @var int */
    private $preCommandLocationChildrenCount;

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content-create-with-assign-parent-location-action.yaml');
    }

    protected function preCommandAssertions(): void
    {
        parent::preCommandAssertions();

        $children = $this->getLocationChildren();
        $this->preCommandLocationChildrenCount = $children->totalCount;
    }

    protected function postCommandAssertions(): void
    {
        parent::postCommandAssertions();

        $children = $this->getLocationChildren();
        self::assertSame($this->preCommandLocationChildrenCount + 1, $children->totalCount);
        self::assertTrue($this->findContentInLocations($this->content, $children));
    }

    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Location> $locations
     */
    private function findContentInLocations(Content $content, iterable $locations): bool
    {
        foreach ($locations as $location) {
            if ($location->getContent()->id === $content->id) {
                return true;
            }
        }

        return false;
    }

    private function getLocationChildren(): LocationList
    {
        $locationService = self::getLocationService();
        $location = $locationService->loadLocation(self::KNOWN_LOCATION_ID);

        return $locationService->loadLocationChildren($location);
    }
}

class_alias(MigrateCommandContentCreateWithAssignParentLocationActionTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandContentCreateWithAssignParentLocationActionTest');
