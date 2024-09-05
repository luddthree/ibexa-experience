<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Installer;

use Ibexa\Installer\Provisioner\AbstractMigrationProvisioner;

/**
 * @internal
 */
final class DashboardProvisioner extends AbstractMigrationProvisioner
{
    protected function getMigrationFiles(): array
    {
        return [
            '2023_12_05_17_00_personalization_dashboard_structure.yaml' => 'dashboard_structure.yaml',
        ];
    }

    protected function getMigrationDirectory(): string
    {
        return '@IbexaPersonalizationBundle/Resources/migrations';
    }
}
