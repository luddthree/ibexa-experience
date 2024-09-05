<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\SalesRepresentatives;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Contracts\CorporateAccount\SalesRepresentatives\SalesRepresentativesFilterBuilderInterface;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;

/**
 * @internal
 */
final class ParentGroupFilterBuilder implements SalesRepresentativesFilterBuilderInterface
{
    private CorporateAccountConfiguration $configuration;

    private LocationService $locationService;

    public function __construct(
        CorporateAccountConfiguration $configuration,
        LocationService $locationService
    ) {
        $this->configuration = $configuration;
        $this->locationService = $locationService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function buildFilterForGetAllQuery(int $offset, int $limit): Filter
    {
        $userGroupLocation = $this->locationService->loadLocationByRemoteId(
            $this->configuration->getSalesRepUserGroupRemoteId()
        );
        $filter = new Filter();
        $filter
            ->withCriterion(new Criterion\IsUserBased())
            ->andWithCriterion(
                new Criterion\Subtree($userGroupLocation->getPathString())
            )
            ->withOffset($offset)
            ->withLimit($limit)
            ->withSortClause(new SortClause\ContentName());

        return $filter;
    }
}
