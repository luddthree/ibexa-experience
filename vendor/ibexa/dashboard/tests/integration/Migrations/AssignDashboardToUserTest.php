<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Dashboard\Migrations;

use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Tests\Integration\Dashboard\DashboardIntegrationTestCase;

final class AssignDashboardToUserTest extends DashboardIntegrationTestCase
{
    protected UserPreferenceService $userPreferenceService;

    protected function setUp(): void
    {
        $testCore = $this->getIbexaTestCore();
        $testCore->setAdministratorUser();

        $this->userPreferenceService = $testCore->getUserPreferenceService();
    }

    public function testDashboardIsAssigned(): void
    {
        $this->executeMigration('assign_dashboard_to_user.yaml');

        $userPreference = $this->userPreferenceService->getUserPreference('active_dashboard');
        self::assertSame('74a7566bf8f60d4cd167aad32d8cc9ba', $userPreference->value);
    }
}
