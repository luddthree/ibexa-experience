<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Dashboard;

use Ibexa\Contracts\Dashboard\DashboardServiceInterface;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase as TestCoreIbexaKernelTestCase;
use Ibexa\Migration\Repository\Migration;

abstract class DashboardIntegrationTestCase extends TestCoreIbexaKernelTestCase
{
    protected function getDashboardService(): DashboardServiceInterface
    {
        return $this->getIbexaTestCore()->getServiceByClassName(DashboardServiceInterface::class);
    }

    protected function executeMigration(string $name): void
    {
        $path = __DIR__ . '/_migrations/' . $name;

        $content = file_get_contents($path);
        if ($content !== false) {
            /** @var \Ibexa\Contracts\Migration\MigrationService $migrationService */
            $migrationService = self::getContainer()->get(MigrationService::class);
            $migrationService->executeOne(new Migration(uniqid(), $content));
        } else {
            self::fail(sprintf('Unable to load "%s" fixture', $path));
        }
    }
}
