<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Service;

use Ibexa\Contracts\SiteFactory\Service\PublicAccessServiceInterface;
use Ibexa\Contracts\SiteFactory\Values\Query\Criterion\MatchAll;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccessList;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;
use Ibexa\SiteFactory\Persistence\PublicAccess\Handler\HandlerInterface;
use Ibexa\SiteFactory\SiteDomainMapper;

class PublicAccessService implements PublicAccessServiceInterface
{
    /** @var \Ibexa\SiteFactory\SiteDomainMapper */
    private $mapper;

    /** @var \Ibexa\SiteFactory\Persistence\PublicAccess\Handler\HandlerInterface */
    private $publicAccessHandler;

    public function __construct(
        SiteDomainMapper $mapper,
        HandlerInterface $publicAccessHandler
    ) {
        $this->mapper = $mapper;
        $this->publicAccessHandler = $publicAccessHandler;
    }

    public function loadPublicAccess(string $identifier): ?PublicAccess
    {
        return $this->publicAccessHandler->load($identifier);
    }

    public function loadPublicAccesses(SiteQuery $query = null): PublicAccessList
    {
        if ($query === null) {
            $query = new SiteQuery();
            $query->criteria = new MatchAll();
        }

        $results = $this->publicAccessHandler->find($query);

        $totalCount = $results['count'];
        if ($totalCount === 0) {
            return new PublicAccessList($totalCount);
        }

        $items = $this->mapper->buildPublicAccessDomainObjectList($results['items']);

        return new PublicAccessList($totalCount, $items);
    }
}

class_alias(PublicAccessService::class, 'EzSystems\EzPlatformSiteFactory\Service\PublicAccessService');
