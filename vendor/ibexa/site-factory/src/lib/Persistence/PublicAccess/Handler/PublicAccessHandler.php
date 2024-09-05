<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Persistence\PublicAccess\Handler;

use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;
use Ibexa\SiteFactory\Persistence\PublicAccess\Gateway\DoctrineGateway;
use Ibexa\SiteFactory\SiteDomainMapper;

class PublicAccessHandler implements HandlerInterface
{
    /** @var \Ibexa\SiteFactory\Persistence\PublicAccess\Gateway\AbstractGateway */
    private $gateway;

    /** @var \Ibexa\SiteFactory\SiteDomainMapper */
    private $mapper;

    public function __construct(
        DoctrineGateway $gateway,
        SiteDomainMapper $mapper
    ) {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
    }

    public function load(string $identifier): ?PublicAccess
    {
        $siteData = $this->gateway->loadPublicAccessData($identifier);

        if ($siteData === null) {
            return null;
        }

        return $this->mapper->buildPublicAccessDomainObject($siteData);
    }

    public function find(SiteQuery $query): array
    {
        $results = $this->gateway->find(
            $query->criteria,
            $query->offset,
            $query->limit
        );

        return [
            'count' => $results['count'],
            'items' => $results['rows'],
        ];
    }

    public function match(string $host): array
    {
        return $this->gateway->match($host);
    }
}

class_alias(PublicAccessHandler::class, 'EzSystems\EzPlatformSiteFactory\Persistence\PublicAccess\Handler\PublicAccessHandler');
