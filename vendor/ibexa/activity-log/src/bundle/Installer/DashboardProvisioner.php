<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Installer;

use Ibexa\Installer\Provisioner\AbstractMigrationProvisioner;

final class DashboardProvisioner extends AbstractMigrationProvisioner
{
    protected function getMigrationFiles(): array
    {
        return [
            '2023_12_04_13_34_activity_log_dashboard_structure.yaml' => 'dashboard_structure.yaml',
        ];
    }

    protected function getMigrationDirectory(): string
    {
        return '@IbexaActivityLogBundle/Resources/migrations';
    }
}
