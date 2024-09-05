<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\Commerce\Individuals;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\CorporateAccount\Commerce\Individuals\IndividualsLocationResolver;
use PHPUnit\Framework\TestCase;

final class IndividualsLocationResolverTest extends TestCase
{
    private const EXAMPLE_LOCATION_ID = 1;
    private const EXAMPLE_USER_GROUP_ID = 23;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ConfigResolverInterface $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService&\PHPUnit\Framework\MockObject\MockObject */
    private UserService $userService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService&\PHPUnit\Framework\MockObject\MockObject */
    private LocationService $locationService;

    private IndividualsLocationResolver $resolver;

    protected function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->userService = $this->createMock(UserService::class);
        $this->locationService = $this->createMock(LocationService::class);

        $this->resolver = new IndividualsLocationResolver(
            $this->locationService,
            $this->userService,
            $this->configResolver
        );
    }

    public function testResolveLocation(): void
    {
        $this->configResolver
            ->method('hasParameter')
            ->with(
                'user_group_location.private',
                'ibexa.commerce.site_access.config.core'
            )
            ->willReturn(false);

        $this->configResolver
            ->method('getParameter')
            ->with('user_registration.group_id')
            ->willReturn(self::EXAMPLE_USER_GROUP_ID);

        $expectedLocation = $this->createMock(Location::class);

        $this->userService
            ->method('loadUserGroup')
            ->with(self::EXAMPLE_USER_GROUP_ID)
            ->willReturn($this->createUserGroupWithLocation($expectedLocation));

        self::assertSame(
            $expectedLocation,
            $this->resolver->resolveLocation()
        );
    }

    public function testResolveLocationForLegacyCommerce(): void
    {
        $expectedLocation = $this->createMock(Location::class);

        $this->configurePrivateCustomersUserGroupLocationId(self::EXAMPLE_LOCATION_ID);

        $this->locationService
            ->method('loadLocation')
            ->with(self::EXAMPLE_LOCATION_ID)
            ->willReturn($expectedLocation);

        self::assertSame(
            $expectedLocation,
            $this->resolver->resolveLocation()
        );
    }

    private function createUserGroupWithLocation(Location $location): UserGroup
    {
        $contentInfo = $this->createMock(ContentInfo::class);
        $contentInfo->method('getMainLocation')->willReturn($location);

        $userGroup = $this->createMock(UserGroup::class);
        $userGroup->method('__get')->with('contentInfo')->willReturn($contentInfo);

        return $userGroup;
    }

    private function configurePrivateCustomersUserGroupLocationId(int $locationId): void
    {
        $args = [
            'user_group_location.private',
            'ibexa.commerce.site_access.config.core',
        ];

        $this->configResolver
            ->method('hasParameter')
            ->with(...$args)
            ->willReturn(true);

        $this->configResolver
            ->method('getParameter')
            ->with(...$args)
            ->willReturn($locationId);
    }
}
