<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Persistence\Site\Handler;

use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\SiteFactory\Persistence\Site\Gateway\AbstractGateway;
use Ibexa\SiteFactory\SiteDomainMapper;

class SiteHandler implements HandlerInterface
{
    /** @var \Ibexa\SiteFactory\Persistence\Site\Gateway\AbstractGateway */
    private $gateway;

    /** @var \Ibexa\SiteFactory\SiteDomainMapper */
    private $mapper;

    /** @var string */
    private $siteFactoryEnabled;

    public function __construct(
        AbstractGateway $gateway,
        SiteDomainMapper $mapper,
        string $siteFactoryEnabled
    ) {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
        $this->siteFactoryEnabled = $siteFactoryEnabled;
    }

    public function find(SiteQuery $query): array
    {
        $result = [
            'count' => 0,
            'items' => [],
        ];

        if (!$this->siteFactoryEnabled) {
            return $result;
        }

        $results = $this->gateway->find(
            $query->criteria,
            $query->offset,
            $query->limit,
        );

        $result['count'] = $results['count'];
        $result['items'] = $results['rows'];

        return $result;
    }

    public function count(SiteQuery $query): int
    {
        if (!$this->siteFactoryEnabled) {
            return 0;
        }

        return $this->gateway->count(
            $query->criteria,
        );
    }

    public function create(SiteCreateStruct $siteCreateStruct): Site
    {
        $siteId = $this->gateway->insert($siteCreateStruct);

        return new Site([
            'id' => $siteId,
            'status' => Site::STATUS_OFFLINE,
            'publicAccesses' => $siteCreateStruct->publicAccesses,
            'name' => $siteCreateStruct->siteName,
            'created' => $siteCreateStruct->siteCreated,
        ]);
    }

    public function update(int $siteId, SiteUpdateStruct $siteUpdateStruct): void
    {
        $this->gateway->update($siteId, $siteUpdateStruct);
    }

    public function load(int $id): Site
    {
        $siteData = $this->gateway->loadSiteData($id);

        if (empty($siteData)) {
            throw new NotFoundException('Site', $id);
        }

        return $this->mapper->buildSiteDomainObject($siteData);
    }

    public function delete(int $id): void
    {
        if (!$this->gateway->canDeleteSite($id)) {
            throw new \LogicException('Cannot delete site: some public assesses are still online');
        }

        $this->gateway->deleteSite($id);
    }
}

class_alias(SiteHandler::class, 'EzSystems\EzPlatformSiteFactory\Persistence\Site\Handler\SiteHandler');
