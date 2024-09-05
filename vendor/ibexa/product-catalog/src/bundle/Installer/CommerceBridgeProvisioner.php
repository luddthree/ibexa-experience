<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Installer;

use Ibexa\Installer\Provisioner\AbstractMigrationProvisioner;

final class CommerceBridgeProvisioner extends AbstractMigrationProvisioner
{
    protected function getMigrationFiles(): array
    {
        return [
            '011_settings.yaml' => 'settings.yaml',
            '2023_01_19_21_32_settings.yaml' => '2023_01_19_21_32_settings.yaml',
        ];
    }

    protected function getMigrationDirectory(): string
    {
        return '@IbexaProductCatalogBundle/Resources/migrations';
    }
}
