<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\CustomerPortal;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\CorporateAccount\CustomerPortal\CustomerPortalResolver as CustomerPortalResolverInterface;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\PageBuilder\SiteAccess\RootLocationProvider;

final class CustomerPortalResolver implements CustomerPortalResolverInterface
{
    private LocationService $locationService;

    private RootLocationProvider $rootLocationProvider;

    private SiteAccessServiceInterface $siteAccessService;

    private int $batchSize;

    /**
     * @var iterable<\Ibexa\Contracts\CorporateAccount\CustomerPortal\PickRule\CustomerPortalPickRule>
     */
    private iterable $pickRules;

    private CorporateAccountConfiguration $corporateAccountConfiguration;

    /**
     * @param iterable<\Ibexa\Contracts\CorporateAccount\CustomerPortal\PickRule\CustomerPortalPickRule> $pickRules
     */
    public function __construct(
        LocationService $locationService,
        RootLocationProvider $rootLocationProvider,
        SiteAccessServiceInterface $siteAccessService,
        CorporateAccountConfiguration $corporateAccountConfiguration,
        int $batchSize = 25,
        iterable $pickRules = []
    ) {
        $this->locationService = $locationService;
        $this->rootLocationProvider = $rootLocationProvider;
        $this->siteAccessService = $siteAccessService;
        $this->corporateAccountConfiguration = $corporateAccountConfiguration;
        $this->batchSize = $batchSize;
        $this->pickRules = $pickRules;
    }

    public function resolveCustomerPortalLocation(Member $member, string $siteAccessName): ?Location
    {
        $rootLocation = $this->rootLocationProvider->getRootLocation(
            $this->siteAccessService->get($siteAccessName)
        );

        if ($rootLocation === null) {
            return null;
        }

        $rootLocationContentType = $rootLocation->getContentInfo()->getContentType();
        $customerPortalContentTypeIdentifier = $this->corporateAccountConfiguration->getContentTypeIdentifier('customer_portal');
        if ($rootLocationContentType->identifier === $customerPortalContentTypeIdentifier) {
            return $rootLocation;
        }

        $portalsBatch = new BatchIterator(
            new CustomerPortalBatchIteratorAdapter($this->locationService, $rootLocation),
            $this->batchSize
        );

        foreach ($this->pickRules as $rule) {
            $location = $rule->pick($member, iterator_to_array($portalsBatch));
            if ($location !== null) {
                return $location;
            }
        }

        return null;
    }
}
