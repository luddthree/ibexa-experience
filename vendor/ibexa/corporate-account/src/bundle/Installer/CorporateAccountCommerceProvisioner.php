<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Installer;

use Ibexa\Bundle\Commerce\Eshop\Api\Configuration\CommerceSiteConfig;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Installer\Provisioner\AbstractMigrationProvisioner;
use Symfony\Component\HttpKernel\KernelInterface;

final class CorporateAccountCommerceProvisioner extends AbstractMigrationProvisioner
{
    private ?CommerceSiteConfig $commerceSiteConfig;

    public function __construct(
        MigrationService $migrationService,
        KernelInterface $kernel,
        ?CommerceSiteConfig $commerceSiteConfig
    ) {
        parent::__construct($migrationService, $kernel);

        $this->commerceSiteConfig = $commerceSiteConfig;
    }

    /**
     * @return array<string, string>
     */
    protected function getMigrationFiles(): array
    {
        if ($this->commerceSiteConfig === null || false === $this->commerceSiteConfig->isCommerceEnabled()) {
            // Skip Legacy Commerce provisioning
            return [];
        }

        return [
            '012_corporate_account_commerce.yml' => 'corporate_account_commerce.yaml',
        ];
    }

    protected function getMigrationDirectory(): string
    {
        return '@IbexaCorporateAccountBundle/Resources/migrations';
    }
}
