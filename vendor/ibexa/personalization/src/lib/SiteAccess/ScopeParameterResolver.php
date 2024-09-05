<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\SiteAccess;

use Ibexa\Core\MVC\Symfony\SiteAccess;

/**
 * @internal for internal use by Personalization integration
 */
interface ScopeParameterResolver
{
    public function getCustomerIdForCurrentScope(): ?int;

    public function getCustomerIdForScope(SiteAccess $siteAccess): ?int;

    public function getLicenseKeyForScope(SiteAccess $siteAccess): ?string;

    public function getHostUrlForScope(SiteAccess $siteAccess): ?string;

    public function getSiteNameForScope(SiteAccess $siteAccess): ?string;
}

class_alias(ScopeParameterResolver::class, 'Ibexa\Platform\Personalization\SiteAccess\ScopeParameterResolver');
