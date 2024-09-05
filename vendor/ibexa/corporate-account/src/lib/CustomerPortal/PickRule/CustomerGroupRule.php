<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\CustomerPortal\PickRule;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\CorporateAccount\CustomerPortal\PickRule\CustomerPortalPickRule;
use Ibexa\Contracts\CorporateAccount\Values\Member;

class CustomerGroupRule implements CustomerPortalPickRule
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $customerGroupsToLocationsMap;

    /**
     * @param array<string, array<int, string>> $customerGroupsToLocationsMap
     */
    public function __construct(
        array $customerGroupsToLocationsMap
    ) {
        $this->customerGroupsToLocationsMap = $customerGroupsToLocationsMap;
    }

    public function pick(Member $member, array $possibleLocations): ?Location
    {
        $customerGroup = $member->getCompany()->getCustomerGroup();

        if (!array_key_exists($customerGroup->getIdentifier(), $this->customerGroupsToLocationsMap)) {
            return null;
        }

        $possibleLocationsMaps = [];
        foreach ($possibleLocations as $possibleLocation) {
            $possibleLocationsMaps[$possibleLocation->remoteId] = $possibleLocation;
        }

        foreach ($this->customerGroupsToLocationsMap[$customerGroup->getIdentifier()] as $remoteLocationId) {
            if (array_key_exists($remoteLocationId, $possibleLocationsMaps)) {
                return $possibleLocationsMaps[$remoteLocationId];
            }
        }

        return null;
    }
}
