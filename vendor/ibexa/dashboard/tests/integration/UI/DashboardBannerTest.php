<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Dashboard;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase as TestCoreIbexaKernelTestCase;
use Ibexa\Dashboard\UI\DashboardBanner;

final class DashboardBannerTest extends TestCoreIbexaKernelTestCase
{
    private UserPreferenceService $userPreferenceService;

    private DashboardBanner $dashboardBanner;

    protected function setUp(): void
    {
        $testCore = $this->getIbexaTestCore();
        $this->userPreferenceService = $testCore->getUserPreferenceService();
        $this->dashboardBanner = $testCore->getServiceByClassName(DashboardBanner::class);
    }

    public function testHideDashboardBanner(): void
    {
        $this->expectException(NotFoundException::class);
        $this->userPreferenceService->getUserPreference(DashboardBanner::USER_PREFERENCE_IDENTIFIER);

        $this->dashboardBanner->hideDashboardBanner();
        $userPreference = $this->userPreferenceService->getUserPreference(
            DashboardBanner::USER_PREFERENCE_IDENTIFIER
        );

        self::assertSame('true', $userPreference->value);
    }
}
