<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Installer;

use Ibexa\Installer\Provisioner\AbstractMigrationProvisioner;

final class DashboardProvisioner extends AbstractMigrationProvisioner
{
    protected function getMigrationFiles(): array
    {
        return [
            '2023_11_20_21_32_product_catalog_dashboard_structure.yaml' => 'dashboard_structure.yaml',
        ];
    }

    protected function getMigrationDirectory(): string
    {
        return '@IbexaProductCatalogBundle/Resources/migrations';
    }
}
