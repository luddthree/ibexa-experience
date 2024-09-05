<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Configuration;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

final class CorporateAccount implements CorporateAccountConfiguration
{
    /** @var array<string, mixed> */
    private array $configuration;

    private ConfigResolverInterface $configResolver;

    /** @param array<string, mixed> $configuration */
    public function __construct(
        array $configuration,
        ConfigResolverInterface $configResolver
    ) {
        $this->configuration = $configuration;
        $this->configResolver = $configResolver;
    }

    public function getContentTypeIdentifier(string $identifier): string
    {
        return (string)$this->configuration[self::CONFIGURATION_CONTENT_TYPE_MAPPINGS][$identifier];
    }

    public function getParentLocationRemoteId(): string
    {
        return (string)$this->configuration[self::CONFIGURATION_PARENT_LOCATION_REMOTE_ID];
    }

    public function getDefaultAdministratorRoleIdentifier(): string
    {
        return (string)$this->configuration[self::CONFIGURATION_DEFAULT_ADMINISTRATOR_ROLE_IDENTIFIER];
    }

    public function getApplicationParentLocationRemoteId(): string
    {
        return (string)$this->configuration[self::CONFIGURATION_APPLICATION_PARENT_LOCATION_REMOTE_ID];
    }

    public function getMemberContentTypeIdentifier(): string
    {
        return $this->getContentTypeIdentifier('member');
    }

    public function getCompanyContentTypeIdentifier(): string
    {
        return $this->getContentTypeIdentifier('company');
    }

    public function getShippingAddressContentTypeIdentifier(): string
    {
        return $this->getContentTypeIdentifier('shipping_address');
    }

    public function getContentTypeGroupIdentifier(): string
    {
        return (string)$this->configuration[self::CONTENT_TYPE_GROUP];
    }

    public function getApplicationContentTypeIdentifier(): string
    {
        return $this->getContentTypeIdentifier('application');
    }

    public function getApplicationStageReasons(string $stageName): array
    {
        $reasons = $this->configResolver->getParameter('corporate_accounts.reasons');

        return array_flip($reasons[$stageName] ?? []);
    }

    public function getSalesRepUserGroupRemoteId(): string
    {
        return (string)$this->configuration[self::CONFIGURATION_SALES_REP_USER_GROUP_REMOTE_ID];
    }

    public function getCorporateAccountsRolesIdentifiers(): array
    {
        return $this->configResolver->getParameter('corporate_accounts.roles');
    }

    public function getCustomerPortalContentTypeIdentifier(): string
    {
        return $this->getContentTypeIdentifier('customer_portal');
    }
}
