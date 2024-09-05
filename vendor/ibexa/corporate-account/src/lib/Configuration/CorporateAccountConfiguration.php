<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Configuration;

interface CorporateAccountConfiguration
{
    public const CONFIGURATION_APPLICATION_PARENT_LOCATION_REMOTE_ID = 'application_parent_location_remote_id';

    public const CONFIGURATION_PARENT_LOCATION_REMOTE_ID = 'parent_location_remote_id';

    public const CONFIGURATION_CONTENT_TYPE_MAPPINGS = 'content_type_mappings';

    public const CONFIGURATION_DEFAULT_ADMINISTRATOR_ROLE_IDENTIFIER = 'default_administrator_role_identifier';

    public const CONFIGURATION_SALES_REP_USER_GROUP_REMOTE_ID = 'sales_rep_user_group_remote_id';

    public const CONTENT_TYPE_GROUP = 'content_type_group_identifier';

    public function getApplicationParentLocationRemoteId(): string;

    public function getParentLocationRemoteId(): string;

    public function getDefaultAdministratorRoleIdentifier(): string;

    public function getContentTypeIdentifier(string $identifier): string;

    public function getMemberContentTypeIdentifier(): string;

    public function getCompanyContentTypeIdentifier(): string;

    public function getShippingAddressContentTypeIdentifier(): string;

    public function getContentTypeGroupIdentifier(): string;

    public function getApplicationContentTypeIdentifier(): string;

    public function getSalesRepUserGroupRemoteId(): string;

    /**
     * @return string[]
     */
    public function getApplicationStageReasons(string $stageName): array;

    /**
     * @return array<string, string>
     */
    public function getCorporateAccountsRolesIdentifiers(): array;

    public function getCustomerPortalContentTypeIdentifier(): string;
}
