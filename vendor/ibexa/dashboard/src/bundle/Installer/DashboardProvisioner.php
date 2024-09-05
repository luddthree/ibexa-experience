<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Installer;

use Ibexa\Installer\Provisioner\AbstractMigrationProvisioner;

final class DashboardProvisioner extends AbstractMigrationProvisioner
{
    protected function getMigrationFiles(): array
    {
        return [
            '2023_09_23_14_15_dashboard_structure.yaml' => 'structure.yaml',
            '2023_10_10_16_14_dashboard_permissions.yaml' => 'permissions.yaml',
        ];
    }

    protected function getMigrationDirectory(): string
    {
        return '@IbexaDashboardBundle/Resources/migrations';
    }
}
