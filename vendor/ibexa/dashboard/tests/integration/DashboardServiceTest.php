<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Dashboard;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Dashboard\DashboardServiceInterface;
use Ibexa\Contracts\Dashboard\Values\DashboardCreateStruct;

final class DashboardServiceTest extends DashboardIntegrationTestCase
{
    private DashboardServiceInterface $dashboardService;

    private LocationService $locationService;

    private UserPreferenceService $userPreferenceService;

    private ConfigResolverInterface $configResolver;

    protected function setUp(): void
    {
        parent::setUp();
        $testCore = $this->getIbexaTestCore();
        $testCore->setAdministratorUser();
        $this->dashboardService = $this->getDashboardService();
        $this->userPreferenceService = $testCore->getUserPreferenceService();
        $this->locationService = $testCore->getLocationService();
        $this->configResolver = $testCore->getServiceByClassName(ConfigResolverInterface::class);
    }

    public function testCustomizeDashboard(): void
    {
        $customizedDashboardDraft = $this->dashboardService->createCustomDashboardDraft();

        self::assertSame(VersionInfo::STATUS_DRAFT, $customizedDashboardDraft->getVersionInfo()->status);
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field $nameField */
        $nameField = $customizedDashboardDraft->getField('name');
        self::assertSame('My dashboard', $nameField->getValue()->text);

        $userDashboardContainerRemoteId = $this->userPreferenceService->getUserPreference(
            'user_dashboards'
        )->value;

        $userDashboardContainerLocation = $this->locationService->loadLocationByRemoteId(
            $userDashboardContainerRemoteId
        );

        /** @var int $locationId */
        $locationId = $customizedDashboardDraft->getVersionInfo()->getContentInfo()->getMainLocationId();
        $customizedDashboardLocation = $this->locationService->loadLocation($locationId);

        self::assertSame(
            $customizedDashboardLocation->parentLocationId,
            $userDashboardContainerLocation->id
        );
    }

    public function testCreateDashboard(): void
    {
        $dashboardCreateStruct = new DashboardCreateStruct();
        $dashboardCreateStruct->name = 'foo';

        $dashboard = $this->dashboardService->createDashboard($dashboardCreateStruct);

        self::assertSame(VersionInfo::STATUS_PUBLISHED, $dashboard->getVersionInfo()->status);
        $dashboardContainerRemoteId = $this->configResolver->getParameter(
            'dashboard.predefined_container_remote_id'
        );
        $dashboardContainerLocation = $this->locationService->loadLocationByRemoteId(
            $dashboardContainerRemoteId
        );

        /** @var int $locationId */
        $locationId = $dashboard->getVersionInfo()->getContentInfo()->getMainLocationId();
        $newDashboardLocation = $this->locationService->loadLocation($locationId);

        self::assertSame(
            $newDashboardLocation->parentLocationId,
            $dashboardContainerLocation->id
        );
    }
}
