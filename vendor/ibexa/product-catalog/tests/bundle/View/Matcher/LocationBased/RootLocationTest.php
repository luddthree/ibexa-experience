<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View\Matcher\LocationBased;

use Ibexa\Bundle\ProductCatalog\View\Matcher\LocationBased\RootLocation;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\Core\MVC\Symfony\View\LocationValueView;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use PHPUnit\Framework\TestCase;

final class RootLocationTest extends TestCase
{
    private const EXAMPLE_VALID_ROOT_REMOTE_ID = 'valid';
    private const EXAMPLE_INVALID_ROOT_REMOTE_ID = 'invalid';

    public function testMatch(): void
    {
        $configProvider = $this->createMock(ConfigProviderInterface::class);
        $configProvider
            ->method('getEngineOption')
            ->with('root_location_remote_id')
            ->willReturn(self::EXAMPLE_VALID_ROOT_REMOTE_ID);

        $matcher = new RootLocation($configProvider);

        self::assertTrue(($matcher)->match($this->createLocationValueView(self::EXAMPLE_VALID_ROOT_REMOTE_ID)));
        self::assertFalse(($matcher)->match($this->createLocationValueView(self::EXAMPLE_INVALID_ROOT_REMOTE_ID)));
        self::assertFalse(($matcher)->match($this->createMock(View::class)));
    }

    private function createLocationValueView(string $remoteId): View
    {
        $location = $this->createLocation($remoteId);

        return new class($location) extends BaseView implements LocationValueView {
            private Location $location;

            public function __construct(Location $location)
            {
                parent::__construct();

                $this->location = $location;
            }

            public function getLocation(): Location
            {
                return $this->location;
            }
        };
    }

    private function createLocation(string $remoteId): Location
    {
        $location = $this->createMock(Location::class);
        $location->method('__get')->with('remoteId')->willReturn($remoteId);

        return $location;
    }
}
