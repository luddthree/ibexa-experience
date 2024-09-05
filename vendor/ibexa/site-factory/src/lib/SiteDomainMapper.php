<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory;

use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteAccessMatcherConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteConfiguration;

/**
 * @internal
 */
class SiteDomainMapper
{
    /** @var \Ibexa\SiteFactory\DesignRegistry */
    private $designRegistry;

    public function __construct(
        DesignRegistry $designRegistry
    ) {
        $this->designRegistry = $designRegistry;
    }

    public function buildSiteDomainObject(array $siteAccesses): Site
    {
        $site = new Site();
        foreach ($siteAccesses as $siteAccess) {
            if (empty($site->id)) {
                $site->id = (int)$siteAccess['id'];
                $site->name = $siteAccess['name'];
                $site->created = (new \DateTimeImmutable())->setTimestamp((int)$siteAccess['created']);
            }
        }

        $site->publicAccesses = $this->mapSiteAccesses($siteAccesses);
        $languages = [];
        $designName = '';
        $group = '';
        foreach ($site->publicAccesses as $siteAccess) {
            $languages[] = $siteAccess->getMainLanguage();
            $designName = $siteAccess->getDesign();
            $group = $siteAccess->getSiteAccessGroup();
        }

        $site->template = $this->designRegistry->getTemplate($designName, $group);
        $site->languages = array_unique($languages);
        $site->status = $this->getStatus($site->publicAccesses);

        return $site;
    }

    /**
     * @param \Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess[] $siteAccesses
     *
     * @return int
     */
    public function getStatus(array $siteAccesses): int
    {
        $status = Site::STATUS_OFFLINE;

        foreach ($siteAccesses as $siteAccess) {
            if ($siteAccess->getStatus() === PublicAccess::STATUS_ONLINE) {
                return Site::STATUS_ONLINE;
            }
        }

        return $status;
    }

    /**
     * @param array $sitesData
     *
     * @return \Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess[]
     */
    public function mapSiteAccesses(array $sitesData): array
    {
        $sites = [];
        foreach ($sitesData as $siteData) {
            $sites[] = $this->buildPublicAccessDomainObject($siteData);
        }

        return $sites;
    }

    public function buildPublicAccessDomainObject($site): PublicAccess
    {
        return new PublicAccess(
            $site['public_access_identifier'],
            (int)$site['site_id'],
            $site['site_access_group'],
            new SiteAccessMatcherConfiguration(
                $site['site_matcher_host'],
                $site['site_matcher_path'],
            ),
            new SiteConfiguration(json_decode($site['config'], true)),
            (int)$site['status']
        );
    }

    public function buildSitesDomainObjectList(array $sitesData): array
    {
        $siteData = [];
        foreach ($sitesData as $site) {
            $siteData[$site['id']][] = $site;
        }

        $sites = [];
        foreach ($siteData as $data) {
            $sites[] = $this->buildSiteDomainObject($data);
        }

        return $sites;
    }

    public function buildPublicAccessDomainObjectList(array $sites): array
    {
        return array_map(function (array $site) {
            return $this->buildPublicAccessDomainObject($site);
        }, $sites);
    }
}

class_alias(SiteDomainMapper::class, 'EzSystems\EzPlatformSiteFactory\SiteDomainMapper');
