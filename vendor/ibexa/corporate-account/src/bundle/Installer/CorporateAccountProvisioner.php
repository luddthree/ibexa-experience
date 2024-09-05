<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Installer;

use Ibexa\Installer\Provisioner\AbstractMigrationProvisioner;

final class CorporateAccountProvisioner extends AbstractMigrationProvisioner
{
    /**
     * @return array<string, string>
     */
    protected function getMigrationFiles(): array
    {
        return [
            '012_corporate_account.yml' => 'corporate_account.yaml',
            '012_corporate_account_registration.yml' => 'corporate_account_registration.yaml',
            '013_assign_role_limitations.yml' => 'assign_role_limitations.yaml',
            '2022_11_07_22_46_application_internal_fields.yaml' => 'application_internal_fields.yaml',
            '2023_03_06_13_00_customer_portal.yaml' => 'customer_portal.yaml',
            '2023_05_09_12_40_corporate_access_role_update.yaml' => '2023_05_09_12_40_corporate_access_role_update.yaml',
            '2023_08_11_12_40_required_billing_address.yaml' => '2023_08_11_12_40_required_billing_address.yaml',
        ];
    }

    protected function getMigrationDirectory(): string
    {
        return '@IbexaCorporateAccountBundle/Resources/migrations';
    }
}
