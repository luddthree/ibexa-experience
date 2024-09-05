<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Security\Limitation\Loader;

use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Personalization\SiteAccess\ScopeParameterResolver;

final class PersonalizationLimitationListLoader implements PersonalizationLimitationListLoaderInterface
{
    private SiteAccessServiceInterface $siteAccessService;

    private ScopeParameterResolver $scopeParameterResolver;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ScopeParameterResolver $scopeParameterResolver
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->scopeParameterResolver = $scopeParameterResolver;
    }

    public function getList(): array
    {
        $configuredCustomerIdList = [];

        foreach ($this->siteAccessService->getAll() as $siteAccess) {
            $customerId = $this->scopeParameterResolver->getCustomerIdForScope($siteAccess);

            if (!$customerId) {
                continue;
            }
            $siteName = $this->scopeParameterResolver->getSiteNameForScope($siteAccess);

            $configuredCustomerIdList[$customerId] = (null !== $siteName)
                ? "{$customerId}: {$siteName}"
                : $customerId;
        }

        return $configuredCustomerIdList;
    }
}

class_alias(PersonalizationLimitationListLoader::class, 'Ibexa\Platform\Personalization\Security\Limitation\Loader\PersonalizationLimitationListLoader');
