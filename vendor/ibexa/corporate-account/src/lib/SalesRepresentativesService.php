<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\CorporateAccount\SalesRepresentatives\SalesRepresentativesFilterBuilderInterface;
use Ibexa\Contracts\CorporateAccount\Service\SalesRepresentativesService as SalesRepresentativesServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\SalesRepresentativesList;

/**
 * @internal
 */
final class SalesRepresentativesService implements SalesRepresentativesServiceInterface
{
    private UserService $userService;

    private LocationService $locationService;

    private SalesRepresentativesFilterBuilderInterface $filterBuilder;

    public function __construct(
        UserService $userService,
        LocationService $locationService,
        SalesRepresentativesFilterBuilderInterface $filterBuilder
    ) {
        $this->userService = $userService;
        $this->locationService = $locationService;
        $this->filterBuilder = $filterBuilder;
    }

    public function getAll(int $offset = 0, int $limit = 25): SalesRepresentativesList
    {
        $filter = $this->filterBuilder->buildFilterForGetAllQuery($offset, $limit);

        $salesRepresentativesList = new SalesRepresentativesList();
        foreach ($this->locationService->find($filter) as $location) {
            $salesRepresentativesList->append($this->userService->loadUser($location->getContent()->id));
        }

        return $salesRepresentativesList;
    }
}
