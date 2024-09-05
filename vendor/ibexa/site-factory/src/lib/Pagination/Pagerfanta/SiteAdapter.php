<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Pagination\Pagerfanta;

use ArrayIterator;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;
use Pagerfanta\Adapter\AdapterInterface;

final class SiteAdapter implements AdapterInterface
{
    /** @var \Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface */
    private $siteService;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery */
    private $query;

    public function __construct(
        SiteServiceInterface $siteService,
        SiteQuery $query
    ) {
        $this->siteService = $siteService;
        $this->query = $query;
    }

    public function getNbResults(): int
    {
        if (isset($this->nbResults)) {
            return $this->nbResults;
        }

        $countQuery = clone $this->query;
        $countQuery->limit = -1;

        $count = $this->siteService->countSites(
            $countQuery
        );

        return $this->nbResults = $count;
    }

    /**
     * Returns an slice of the results.
     *
     * @param int $offset the offset
     * @param int $length the length
     */
    public function getSlice($offset, $length): iterable
    {
        $query = clone $this->query;
        $query->offset = $offset;
        $query->limit = $length;
        $searchResult = $this->siteService->loadSites($query);

        if (!isset($this->nbResults) && isset($searchResult->totalCount)) {
            $this->nbResults = $searchResult->totalCount;
        }

        return new ArrayIterator(array_merge($searchResult->getSites(), $searchResult->getPages()));
    }
}

class_alias(SiteAdapter::class, 'EzSystems\EzPlatformSiteFactory\Pagination\Pagerfanta\SiteAdapter');
