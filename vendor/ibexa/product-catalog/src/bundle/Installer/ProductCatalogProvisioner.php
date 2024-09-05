<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Installer;

use Ibexa\Installer\Provisioner\AbstractMigrationProvisioner;

final class ProductCatalogProvisioner extends AbstractMigrationProvisioner
{
    protected function getMigrationFiles(): array
    {
        return [
            '009_product_catalog.yml' => 'product_catalog.yaml',
            '010_currencies.yml' => 'currencies.yaml',
            '2022_06_23_09_39_product_categories.yaml' => '2022_06_23_09_39_product_categories.yaml',
        ];
    }

    protected function getMigrationDirectory(): string
    {
        return '@IbexaProductCatalogBundle/Resources/migrations';
    }
}
