<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Dashboard;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Dashboard\DashboardServiceInterface;

final class DashboardServiceAuthorizationTest extends DashboardIntegrationTestCase
{
    private DashboardServiceInterface $dashboardService;

    protected function setUp(): void
    {
        parent::setUp();
        $testCore = $this->getIbexaTestCore();
        $testCore->setAnonymousUser();
        $this->dashboardService = $this->getDashboardService();
    }

    public function testCreateDashboard(): void
    {
        $this->expectUnauthorizedException('dashboard', 'customize');
        $location = $this->createMock(Location::class);
        $location->method('__get')
            ->with('remoteId')
            ->willReturn('foo');
        $this->dashboardService->createCustomDashboardDraft($location);
    }

    private function expectUnauthorizedException(string $module, string $function): void
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage(
            sprintf('The User does not have the \'%s\' \'%s\' permission', $function, $module)
        );
    }
}
